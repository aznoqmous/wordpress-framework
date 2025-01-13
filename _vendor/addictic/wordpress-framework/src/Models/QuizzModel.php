<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;
use PHPMailer\PHPMailer\PHPMailer;

class QuizzModel extends AbstractPostTypeModel
{
    protected static $strName = "quizz";

    public function renderResult($data)
    {
        $score = 0;
        foreach ($this->questions as $i => $question) {
            $valid = true;
            foreach ($question['answers'] as $j => $answer) {
                if (isset($data['question'][$i][$j]) != isset($answer['isRightAnswer'])) $valid = false;
            }
            if ($valid) $score++;
        }

        $ratio = $score / count($this->questions);

        $level = "beginner";
        if ($ratio > 0.3) $level = "intermediate";
        elseif ($ratio > 0.7) $level = "advanced";
        $levelLang = Container::get("translator")->trans("quizz.level.$level");

        return Container::get("twig")->render("quizz/mail.twig", array_merge([
            'data' => $data,
            'score' => $score,
            'levelLang' => $levelLang
        ], $this->row()));
    }

    public function sendResult($payload, $emailTo)
    {
        $translator = Container::get("translator");

        $htmlContent = $this->renderResult($payload);
        $textContent = strip_tags($htmlContent);
        $senderMail = get_option("ifcMail");

        $mail = new PHPMailer();
        $mail->From = $senderMail ?: "noreply@example.com";
        $mail->FromName = "IFC Tritech";
        $mail->WordWrap = 50;
        $mail->Subject = $translator->trans("quizz.email.subject");
        $mail->Body = $htmlContent;
        $mail->AltBody = $textContent;
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        $mail->addAddress($emailTo);
        $mail->isHTML();
        $mail->send();
    }

    public function getTheme()
    {
        $terms = $this->getTerms("theme")->getModels();
        return $terms ? $terms[0] : null;
    }
}