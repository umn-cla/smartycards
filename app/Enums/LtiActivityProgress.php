<?php

namespace App\Enums;


// https: //www.imsglobal.org/node/161981#activityprogress
enum LtiActivityProgress: string
{
    case Initialized = 'Initialized';
    case Started = 'Started';
    case InProgress = 'InProgress';
    case Submitted = 'Submitted';
    case Completed = 'Completed';
}
