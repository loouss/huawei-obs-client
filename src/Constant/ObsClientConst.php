<?php

namespace Loouss\ObsClient\Constant;

class ObsClientConst
{
    const SDK_VERSION = '3.21.6';

    const AclPrivate = 'private';
    const AclPublicRead = 'public-read';
    const AclPublicReadWrite = 'public-read-write';
    const AclPublicReadDelivered = 'public-read-delivered';
    const AclPublicReadWriteDelivered = 'public-read-write-delivered';

    const AclAuthenticatedRead = 'authenticated-read';
    const AclBucketOwnerRead = 'bucket-owner-read';
    const AclBucketOwnerFullControl = 'bucket-owner-full-control';
    const AclLogDeliveryWrite = 'log-delivery-write';

    const StorageClassStandard = 'STANDARD';
    const StorageClassWarm = 'WARM';
    const StorageClassCold = 'COLD';

    const PermissionRead = 'READ';
    const PermissionWrite = 'WRITE';
    const PermissionReadAcp = 'READ_ACP';
    const PermissionWriteAcp = 'WRITE_ACP';
    const PermissionFullControl = 'FULL_CONTROL';

    const AllUsers = 'Everyone';

    const GroupAllUsers = 'AllUsers';
    const GroupAuthenticatedUsers = 'AuthenticatedUsers';
    const GroupLogDelivery = 'LogDelivery';

    const RestoreTierExpedited = 'Expedited';
    const RestoreTierStandard = 'Standard';
    const RestoreTierBulk = 'Bulk';

    const GranteeGroup = 'Group';
    const GranteeUser = 'CanonicalUser';

    const CopyMetadata = 'COPY';
    const ReplaceMetadata = 'REPLACE';

    const SignatureV2 = 'v2';
    const SignatureV4 = 'v4';
    const SigantureObs = 'obs';

    const ObjectCreatedAll = 'ObjectCreated:*';
    const ObjectCreatedPut = 'ObjectCreated:Put';
    const ObjectCreatedPost = 'ObjectCreated:Post';
    const ObjectCreatedCopy = 'ObjectCreated:Copy';
    const ObjectCreatedCompleteMultipartUpload = 'ObjectCreated:CompleteMultipartUpload';
    const ObjectRemovedAll = 'ObjectRemoved:*';
    const ObjectRemovedDelete = 'ObjectRemoved:Delete';
    const ObjectRemovedDeleteMarkerCreated = 'ObjectRemoved:DeleteMarkerCreated';

    const REQUEST_RESOURCE = \Loouss\ObsClient\Constant\ObsRequestResource::class;
    const OBS_CONSTANT = \Loouss\ObsClient\Constant\ObsConstants::class;
}
