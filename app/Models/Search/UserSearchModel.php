<?php

namespace App\Models\Search;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserSearchModel extends Model
{
    protected $table='users';

    public function search($key)
    {
        $result=[];
        if (strlen($key)>=2) {
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
            $ret=self::where('email', $key)
                ->orWhereRaw($sql, [$key])
                ->select('id', 'avatar', 'name', 'describes', 'professional_rate')
                ->orderBy('professional_rate', 'DESC')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }
        return $result;
    }
}
