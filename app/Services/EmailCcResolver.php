<?php

namespace App\Services;

use App\Models\ParticularSoftConfiguration;
use App\Models\Customer;

class EmailCcResolver
{
    /**
     * @param \App\Models\Customer|null $customer
     * @return array CC finales (normalizados y sin duplicados)
     */
    public function resolve(?Customer $customer): array
    {
        $sets = [];

        // 1) CC por customer
        if ($customer && $customer->cc_emails) $sets[] = $customer->cc_emails;

        // 2) CC global (PSC)
        $psc = \App\Models\ParticularSoftConfiguration::query()->latest('id')->first();
        if ($psc?->cc_mail_trafico_Team) $sets[] = $psc->cc_mail_trafico_Team;

        // Normalizar y deduplicar
        $emails = [];
        foreach ($sets as $s) {
            foreach (preg_split('/[;,]/', $s) as $raw) {
                $e = trim($raw);
                if ($e) $emails[strtolower($e)] = $e;
            }
        }
        return array_values($emails);
    }
}