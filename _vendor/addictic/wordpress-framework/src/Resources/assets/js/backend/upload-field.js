import Element from "../libs/element"

export default class UploadField extends Element {
    build() {
        this.button = this.select('input[type="button"]')
        this.hiddenInput = this.select('input[type="hidden"]')
        this.previewContainer = this.select(".preview-container")
    }

    updatePreview(files) {
        this.previewContainer.innerHTML = ""
        files.filter(file => file && file.url).map(file => {
            const figure = this.create("figure", {}, this.previewContainer)
            if(file.type == "image") this.create("img", {src: file.url}, figure)
            this.create("span", {innerHTML: file.filename}, figure)
        })
    }

    bind() {
        this.button.addEventListener('click', (e) => {
            let mediaUploader;
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.uploader.param("post_id", this.hiddenInput.value)
                mediaUploader.open();
                return;
            }
            wp.media.model.settings.post.id = this.hiddenInput.value
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: this.isMultiple ? 'Choisir des fichiers' : 'Choisir un fichier',
                button: {
                    text: this.isMultiple ? 'Choisir des fichiers' : 'Choisir un fichier'
                },
                library: {
                    type: this.container.dataset.filetype
                },
                multiple: this.isMultiple
            });
            mediaUploader.on('open', () => {
                const selection = mediaUploader.state().get('selection');
                const ids = this.hiddenInput.value.split(',');

                ids.forEach(function (id) {
                    const attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                });
            })
            mediaUploader.on('select', () => {
                const files = Array.from(mediaUploader
                    .state()
                    .get('selection')).map(n => n.attributes)
                this.hiddenInput.value = files.map(f => f.id).filter(v => v).join(',')
                this.updatePreview(files)
            });
            mediaUploader.open();
        })
    }

    get isMultiple(){
        return !!this.container.dataset.multiple
    }
}