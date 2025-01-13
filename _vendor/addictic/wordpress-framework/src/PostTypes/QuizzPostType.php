<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\Framework\CheckboxField;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\ListField;
use Addictic\WordpressFramework\Fields\AnswersField;

/**
 * @PostType(name="quizz", icon="dashicons-forms", taxonomies="theme", priority=1)
 */
class QuizzPostType extends AbstractPostType
{

    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true
        ]);

        $this
            ->addMetabox("content")
            ->addField(new ListField("questions", [
                'fields' => [
                    "question" => new Field("question"),
                    "answers" => new ListField("answers", [
                        "fields" => [
                            "answer" => new Field("answer", [
                                'mandatory' => true,
                                'class' => "w50"
                            ]),
                            "isRightAnswer" => new CheckboxField("isRightAnswer", [
                                'mandatory' => true,
                                'class' => "w50"
                            ])
                        ]
                    ]),
                    "explanation" => new Field("explanation", [
                        'mandatory' => false
                    ])
                ]
            ]))
            ->applyToPostType("quizz");
    }
}