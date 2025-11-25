<?php

namespace App\Enums;

class WorkPermitStatus
{
    // ========== AÇILIŞ AŞAMALARI ==========
    const PENDING_UNIT_APPROVAL = 'pending_unit_approval';
    const PENDING_AREA_APPROVAL = 'pending_area_approval';
    const PENDING_SAFETY_APPROVAL = 'pending_safety_approval';
    const PENDING_EMPLOYER_APPROVAL = 'pending_employer_approval';
    const APPROVED = 'approved';

    // ========== KAPATMA AŞAMALARI ==========
    const WORK_COMPLETED = 'work_completed';
    const PENDING_AREA_CLOSE = 'pending_area_close';
    const PENDING_SAFETY_CLOSE = 'pending_safety_close';
    const PENDING_EMPLOYER_CLOSE = 'pending_employer_close';
    const CLOSED = 'closed';

    // ========== DİĞER DURUMLAR ==========
    const REJECTED = 'rejected';

    /**
     * Durumların Türkçe karşılıkları
     */
    public static function labels(): array
    {
        return [
            self::PENDING_UNIT_APPROVAL => 'Birim Amiri Onayı Bekliyor',
            self::PENDING_AREA_APPROVAL => 'Alan Amiri Onayı Bekliyor',
            self::PENDING_SAFETY_APPROVAL => 'İSG Uzmanı Onayı Bekliyor (Açılış)',
            self::APPROVED => 'İş Devam Ediyor',
            self::WORK_COMPLETED => 'İş Tamamlandı - Kapatma Başlatıldı',
            self::PENDING_AREA_CLOSE => 'Alan Amiri Kapatma Onayı Bekliyor',
            self::PENDING_SAFETY_CLOSE => 'İSG Uzmanı Kapatma Onayı Bekliyor',
            self::PENDING_EMPLOYER_CLOSE => 'İşveren Vekili Final Onayı Bekliyor',
            self::CLOSED => 'Kapatıldı',
            self::REJECTED => 'Reddedildi',
        ];
    }

    /**
     * Sonraki duruma geç
     */
    public static function getNextStatus(string $currentStatus): ?string
    {
        $flow = [
            // AÇILIŞ AKIŞI
            self::PENDING_AREA_APPROVAL => self::PENDING_SAFETY_APPROVAL,
            self::PENDING_SAFETY_APPROVAL => self::APPROVED,

            // KAPATMA AKIŞI
            self::WORK_COMPLETED => self::PENDING_AREA_CLOSE,
            self::PENDING_AREA_CLOSE => self::PENDING_SAFETY_CLOSE,
            self::PENDING_SAFETY_CLOSE => self::PENDING_EMPLOYER_CLOSE,
            self::PENDING_EMPLOYER_CLOSE => self::CLOSED,
        ];

        return $flow[$currentStatus] ?? null;
    }

    /**
     * Hangi rolün onay vermesi gerektiğini döndür
     */
    public static function getApproverRole(string $status): ?string
    {
        $approvers = [
            self::PENDING_AREA_APPROVAL => 'alan_amiri',
            self::PENDING_SAFETY_APPROVAL => 'isg_uzmani',
            self::PENDING_AREA_CLOSE => 'alan_amiri',
            self::PENDING_SAFETY_CLOSE => 'isg_uzmani',
            self::PENDING_EMPLOYER_CLOSE => 'isveren_vekili',
        ];

        return $approvers[$status] ?? null;
    }
}
