<?php
namespace rest;

class bankHoliday
{
    public static $url = 'https://www.gov.uk/bank-holidays.json';
    public static $mainKey = 'england-and-wales';
    public static $subKey = 'events';
    public static $rowKey = 'date';

    static function bankHolidaysFromStartToEnd(\DateTime $startDate, \DateTime $endDate) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER,         1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER,        FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, self::$url);

        $allBankHolidayJson = curl_exec($ch);

        $allBankHoliday = json_decode($allBankHolidayJson);
        $data = array();

        $bankHolidays = $allBankHoliday->{self::$mainKey}->{self::$subKey};
        foreach($bankHolidays as $row) {
            $dateRaw = $row->{self::$rowKey};
            $date = new \DateTime($dateRaw);
            if ($date >= $startDate && $date <= $endDate) {    
                $data[] = $row->{self::$rowKey};
            }
        }
        $err = curl_error($ch);

        curl_close($ch);

        return array(
            'amount' => count($data),
            'list' => $data
        );
    }
}