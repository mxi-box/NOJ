<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;
use DB;

class GroupSearchModel extends Model
{
    protected $table='group';
    protected $primaryKey='gid';

    public function search($key)
    {
        $result=[];
        //group name or gcode find
        if (strlen($key)>=2) {
            $ret=self::where(function($query) use ($key){
                switch(DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))
                {
                    case 'mysql':
                        $sql = 'MATCH(`name`) AGAINST (? IN BOOLEAN MODE)';
                        break;
                    case 'pgsql':
                        $sql = "to_tsvector('zh', name) @@ plainto_tsquery('zh', ?)";
                        break;
                    default:
                        throw new \Exception('Driver not supported.');
                }
                $query->whereRaw($sql, [$key])
                    ->orWhere('gcode', $key);
                })
                ->where('public', 1)
                ->select('gid', 'gcode', 'img', 'name', 'description')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }

        return $result;
    }
}
