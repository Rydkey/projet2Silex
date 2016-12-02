<?php
/**
 * Created by PhpStorm.
 * User: rrisser
 * Date: 02/12/16
 * Time: 10:57
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

//bite

class ClientModel {
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getClient(){
        $querybuilder = new QueryBuilder($this->db);
        $querybuilder
            ->select('c.id','c.email','c.login','c.droit')
            ->from('users','c')
            ->where('c.droit!= ?')
            ->orderBy('c.id')
            ->setParameter(0,'DROITadmin');
        return $querybuilder->execute()->fetchAll();
    }



}