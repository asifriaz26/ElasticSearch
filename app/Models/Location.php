<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 *
 * @property integer id
 * @property string title
 * @property string type
 * @property string  parent
 * @property string  role_id
 * @property string  created_by
 *
 * @package App\Models
 */
class Location extends Model
{
    use HasFactory;
    /**
     * @var string
     */
    protected $table = "locations";

    const LOCATION_TYPE_STATIC = 'STATIC';
    const LOCATION_TYPE_LIVE = 'LIVE';
    const LOCATION_TYPE_UNDER_PROCESS = 'UNDER_PROCESS';

    // old locations
    const LOCATION_RECEIVING_DOCK = 'Receiving Dock';
    const LOCATION_LOCKER = 'Locker';
    const LOCATION_WRITE_OFF = 'Writeoff';
    const LOCATION_REPAIR_CENTER = 'Repair Center';
    const LOCATION_RD1 = 'rd1';

    const LOCATION_RETURN_DOCK = 'Return Dock';

    // new locations
    const LOCATION_PO_VARIANCE = 'PO Variance';
    const LOCATION_QC = 'QC';
    const LOCATION_REPAIR_DOCK = 'Repair Dock';
    const LOCATION_QC_FAIL_DOCK = 'QC Fail Dock';

    const LOCATION_PUTAWAY = 'Putaway';
    const LOCATION_DAMAGED = 'Damaged';
    const LOCATION_PO_RETURN = 'PO Return';
    const LOCATION_OFFLINE_SALES = 'Offline Sales';
    const Rtn_Reject_location = 'RTN Rejected';
    const Rls_Live = 'Rls Live';

    /**
     * @var array
     */
    protected $fillable = ["dear_id","name","code","description","created_at","updated_at","parent_id","location_type","warehouse_id","deleted","deleted_by","created_by","deleted_at","default"];

    /**
     * @var array
     */
    protected $guarded = [];

}
