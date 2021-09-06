<?php

namespace Loouss\ObsClient\Http\Common;

use Loouss\ObsClient\Constant\ObsClientConst;

class ObsTransform
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof ObsTransform)) {
            self::$instance = new ObsTransform();
        }
        return self::$instance;
    }


    public function transform($sign, $para)
    {
        if ($sign === 'aclHeader') {
            $para = $this->transAclHeader($para);
        } else {
            if ($sign === 'aclUri') {
                $para = $this->transAclGroupUri($para);
            } else {
                if ($sign == 'event') {
                    $para = $this->transNotificationEvent($para);
                } else {
                    if ($sign == 'storageClass') {
                        $para = $this->transStorageClass($para);
                    }
                }
            }
        }
        return $para;
    }

    private function transAclHeader($para)
    {
        if ($para === ObsClientConst::AclAuthenticatedRead || $para === ObsClientConst::AclBucketOwnerRead ||
            $para === ObsClientConst::AclBucketOwnerFullControl || $para === ObsClientConst::AclLogDeliveryWrite) {
            $para = null;
        }
        return $para;
    }

    private function transAclGroupUri($para)
    {
        if ($para === ObsClientConst::GroupAllUsers) {
            $para = ObsClientConst::AllUsers;
        }
        return $para;
    }

    private function transNotificationEvent($para)
    {
        $pos = strpos($para, 's3:');
        if ($pos !== false && $pos === 0) {
            $para = substr($para, 3);
        }
        return $para;
    }

    private function transStorageClass($para)
    {
        $search = array('STANDARD', 'STANDARD_IA', 'GLACIER');
        $repalce = array(
            ObsClientConst::StorageClassStandard,
            ObsClientConst::StorageClassWarm,
            ObsClientConst::StorageClassCold
        );
        $para = str_replace($search, $repalce, $para);
        return $para;
    }
}

