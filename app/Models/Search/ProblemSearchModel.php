<?php

namespace App\Models\Search;

use App\Models\ProblemModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProblemSearchModel extends Model
{
    protected $table='problem';
    protected $primaryKey='pid';

    public function search($key)
    {
        $result=[];
        if (strlen($key)>=2) {
            switch(DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME))
            {
                case 'mysql':
                    $sql = 'MATCH(`title`) AGAINST (? IN BOOLEAN MODE)';
                    break;
                case 'pgsql':
                    $sql = "to_tsvector('zh', title) @@ plainto_tsquery('zh', ?)";
                    break;
                default:
                    throw new \Exception('Driver not supported.');
            }
            $ret=self::where('pcode', $key)
                ->orWhereRaw($sql, [$key])
                ->select('pid', 'pcode', 'title')
                ->limit(120)
                ->get()->all();
            if (!empty($ret)) {
                $result+=$ret;
            }
        }
        $problemModel=new ProblemModel();
        foreach ($result as $p_index => $p) {
            if ($problemModel->isBlocked($p['pid']) || $problemModel->isHidden($p["pid"])) {
                unset($result[$p_index]);
            }
        }
        return $result;
    }
}
