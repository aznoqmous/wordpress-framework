const sleep = async(time=0)=>{
    return new Promise(resolve => setTimeout(resolve, time));
}
document.addEventListener('DOMContentLoaded', async () => {

    await sleep(100)

    const form = document.querySelector('form[name="post"],form.metabox-location-normal')

    if(!form) return;

    const buttons = Array.from(document.querySelectorAll(".editor-post-publish-button"))
    const updateSubmitButtons = ()=> Array.from(document.querySelectorAll(".editor-post-publish-button")).map(button => {
        button.classList.toggle('disabled', !form.checkValidity())
    })

    form.append(document.createRange().createContextualFragment("<input type='submit' style='opacity: 0; pointer-events: none;'>"))

    updateSubmitButtons()

    form.querySelectorAll("input,select,textarea").forEach(element=>{
        element.addEventListener("input", ()=> updateSubmitButtons())
    })

    form.addEventListener("keyup", ()=> {
        updateSubmitButtons()
    })

    buttons.map(button => button.addEventListener('click', (e)=> {
        if(button.classList.contains('disabled')){
            e.stopImmediatePropagation()
            e.preventDefault()
            form.reportValidity()
        }
    }))
})