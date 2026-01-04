<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\App\Mail\Business\Hydrator;

use Hfm\Kursanmeldung\Domain\Model\Kursanmeldung;

class MailBodyHydrator
{
    /**
     * @param string $body
     * @param $mailDto
     * @return string
     */
    public function hydrate(string $body, $mailDto): string
    {
        $assignments = $mailDto->getAssignments();
        if(!empty($assignments)){
            $body = $this->replaceRegistrationData($body, $assignments);
        }

        return $body;
    }

    /**
     * @param string $html
     * @param array $assignments
     * @return string
     */
    private function replaceRegistrationData(string $html, array $assignments): string
    {
        $html = str_replace('<p>&nbsp;</p>', '', $html);
        $html = str_replace('<p> </p>', '', $html);

        foreach ($assignments as $key => $value){
            if(in_array(gettype($value), ['string','double','integer','NULL']) ){
                $html = str_replace('{' . $key . '}', (string)$value, $html);
            }
        }

        return $html;
    }
}