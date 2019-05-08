<?php

namespace app\modules\cashdesks\models\search;

use Yii;
use yii\data\SqlDataProvider;
use app\modules\cashdesks\helpers\DateTimeHelper;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\AccountableTransact;


class AccountableTransactDebtSearch extends AccountableTransactBaseSearch
{

    public function __construct( $departmentId, $config = array())
    {
        $this->depart_id = $departmentId;
        parent::__construct($config);
    }
  
    public function rules()
    {
        return [
            [
                [
                   'type',
                    'user_id',
                ], 
                'integer'
            ],
        ];
    }
    
    public function search($params)
    {
        $time = CashdesksApi::getCurrentTimestamp();
        
        $dayBeginTime = DateTimeHelper::getDayBeginFromTimestamp($time);
        $dayEndTime = DateTimeHelper::getDayEndFromTimestamp($time);
        
        $totalCount = $this->getTotalCount($dayBeginTime, $dayEndTime);
        $sql = $this->getSqlForDataProvider($dayBeginTime, $dayEndTime);

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $totalCount,
            'sort' => [
                'attributes' => [
                    'type',
                    'debt',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
    
    private function getTotalCount($dayBeginTime, $dayEndTime)
    {
       $command = Yii::$app->db->createCommand($this->getSql(true));
       $this->bindValuesToCommand($command, $dayBeginTime, $dayEndTime);
       return $command->queryScalar();
    }
    
    private function getSqlForDataProvider($dayBeginTime, $dayEndTime)
    {
        $command = Yii::$app->db->createCommand($this->getSql(false));
        $this->bindValuesToCommand($command, $dayBeginTime, $dayEndTime);
        return $command->rawSql;
    }
    
    private function bindValuesToCommand($command, $dayBeginTime, $dayEndTime)
    {
        $departmentId = $this->depart_id;
        $typeAcctabIssue = AccountableTransact::TYPE_ACCTAB_ISSUE;
        $typeAcctabReturn = AccountableTransact::TYPE_ACCTAB_RETURN;
        $typeAcctabIssuePickup = AccountableTransact::TYPE_ACCTAB_ISSUE_PICKUP;
        $typeAcctabReturnPickup = AccountableTransact::TYPE_ACCTAB_RETURN_PICKUP;
        
        $command->bindValue(':departmentId', $departmentId)
            ->bindValue(':dayBeginTime', $dayBeginTime)
            ->bindValue(':dayEndTime', $dayEndTime)
            ->bindValue(':typeAcctabIssue', $typeAcctabIssue)
            ->bindValue(':typeAcctabReturn', $typeAcctabReturn)
            ->bindValue(':typeAcctabIssuePickup', $typeAcctabIssuePickup)
            ->bindValue(':typeAcctabReturnPickup', $typeAcctabReturnPickup);
        
        return $command;
    }
    
    private function getSQL($count = false)
    {
        if ($count) {
            $select = 'COUNT(*)';
        } else {
            $select = '*';
        }
        
        $sql = <<<SQL
SELECT $select FROM (    
                
    SELECT 
        at1.user_id AS user_id, 
        at1.type AS type,     
        COALESCE(SUM(at1.`sum1`), 0) - COALESCE(SUM(at2.`sum2`), 0) AS debt    
    FROM (
            SELECT user_id, type, SUM(`sum`) AS sum1 
            FROM {{%cashdesks_accountable_transact}}
            WHERE 
                    date_create >= :dayBeginTime AND 
                    date_create <= :dayEndTime AND 
                    depart_id = :departmentId AND 
                    type = :typeAcctabIssue
            GROUP BY user_id, type	
        ) AS at1 LEFT JOIN (
            SELECT user_id, SUM(`sum`) AS sum2 
            FROM {{%cashdesks_accountable_transact}}
            WHERE 
                    date_create >= :dayBeginTime AND 
                    date_create <= :dayEndTime AND 
                    depart_id = :departmentId AND 
                    type = :typeAcctabReturn
            GROUP BY user_id
        ) AS at2
    ON at1.user_id = at2.user_id
    GROUP BY at1.user_id, at1.type    
        
    UNION
      
    SELECT 0 as user_id, pickup.* FROM (
            SELECT 
                at1.type AS type,     
                COALESCE(SUM(at1.`sum`), 0) - COALESCE(
                    (
                        SELECT SUM(`sum`) 
                        FROM {{%cashdesks_accountable_transact}}
                        WHERE 
                            date_create >= :dayBeginTime AND 
                            date_create <= :dayEndTime AND 
                            depart_id = :departmentId AND 
                            type = :typeAcctabReturnPickup
                    ),
                    0
                ) AS debt
            FROM {{%cashdesks_accountable_transact}} at1
            WHERE at1.type = :typeAcctabIssuePickup
            GROUP BY at1.type  
        ) AS pickup
                
) AS result
WHERE debt <> 0
SQL;
        return $sql;
    }
    
}
