<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulltextIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        switch(DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME))
        {
            case 'mysql':
                DB::statement('ALTER TABLE `problem` ADD FULLTEXT problem_title_idx(`title`) WITH PARSER ngram');
                DB::statement('ALTER TABLE `group` ADD FULLTEXT group_name_idx(`name`) WITH PARSER ngram');
                DB::statement('ALTER TABLE `contest` ADD FULLTEXT contest_name_idx(`name`) WITH PARSER ngram');
                DB::statement('ALTER TABLE `users` ADD FULLTEXT users_name_idx(`name`) WITH PARSER ngram');
                break;
        
            case 'pgsql':
                DB::statement('CREATE TEXT SEARCH CONFIGURATION zh (PARSER = zhparser)');
                DB::statement('ALTER TEXT SEARCH CONFIGURATION zh ADD MAPPING FOR n,v,a,i,e,l WITH simple');
                DB::statement("CREATE INDEX problem_title_idx ON \"problem\" USING gin(to_tsvector('zh', title))");
                DB::statement("CREATE INDEX group_name_idx ON \"group\" USING gin(to_tsvector('zh', name))");
                DB::statement("CREATE INDEX contest_name_idx ON \"contest\" USING gin(to_tsvector('zh', name))");
                DB::statement("CREATE INDEX users_name_idx ON \"users\" USING gin(to_tsvector('zh', name))");
                break;
        
            default:
                throw new \Exception('Driver not supported.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `problem` DROP INDEX `problem_title_idx`');
        DB::statement('ALTER TABLE `group` DROP INDEX `group_name_idx`');
        DB::statement('ALTER TABLE `contest` DROP INDEX `contest_name_idx`');
        DB::statement('ALTER TABLE `users` DROP INDEX `users_name_idx`');
        if (DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME) == 'pgsql')
        {
            DB::statement('DROP TEXT SEARCH CONFIGURATION zh');
        }
    }
}
