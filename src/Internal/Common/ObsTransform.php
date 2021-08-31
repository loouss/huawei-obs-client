<?php

namespace Loouss\ObsClient\Internal\Common;

use Loouss\ObsClient\ObsClient;

class ObsTransform implements ITransform
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
        if ($para === ObsClient::AclAuthenticatedRead || $para === ObsClient::AclBucketOwnerRead ||
            $para === ObsClient::AclBucketOwnerFullControl || $para === ObsClient::AclLogDeliveryWrite) {
            $para = null;
        }
        return $para;
    }

    private function transAclGroupUri($para)
    {
        if ($para === ObsClient::GroupAllUsers) {
            $para = ObsClient::AllUsers;
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
        $repalce = array(ObsClient::StorageClassStandard, ObsClient::StorageClassWarm, ObsClient::StorageClassCold);
        $para = str_replace($search, $repalce, $para);
        return $para;
    }
}

