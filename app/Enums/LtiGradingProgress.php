<?php

namespace App\Enums;

// see: https://www.imsglobal.org/node/161981#gradingprogres
enum LtiGradingProgress: string
{
    case NotReady = 'NotReady';
    case Pending = 'Pending';
    case Failed = 'Failed';
    case Submitted = 'Submitted';
    case FullyGraded = 'FullyGraded';
}
