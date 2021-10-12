<?php


namespace App\Services\YouthManagementServices;


use App\Models\YouthAddress;

/**
 * Class YouthAddressService
 * @package App\Services\YouthManagementServices
 */
class YouthAddressService
{

    /**
     * @param array $data
     * @return YouthAddress
     */
    public function store(array $data): YouthAddress
    {
        $address = new YouthAddress();
        $address->fill($data);
        $address->save();
        return $address;
    }
}
