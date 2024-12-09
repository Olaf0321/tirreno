<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

namespace Models;

class Rules extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'dshb_operators_rules';

    // returns associative array
    public function getAll(): array {
        $params = [];

        $query = (
            'SELECT
                dshb_rules.id

            FROM
                dshb_rules'
        );

        $results = $this->execQuery($query, $params);

        return \Utils\Rules::activeRulesIds($results);
    }

    public function getAllByOperator(int $apiKey): array {
        $params = [
            ':api_key' => $apiKey,
        ];

        $query = (
            'SELECT
                dshb_rules.id,
                dshb_operators_rules.value,
                dshb_operators_rules.proportion,
                dshb_operators_rules.proportion_updated_at

            FROM
                dshb_rules

            LEFT JOIN dshb_operators_rules
            ON (dshb_rules.id = dshb_operators_rules.rule_id AND dshb_operators_rules.key = :api_key)'
        );

        $results = $this->execQuery($query, $params);

        // attribues filter applied in controller
        return \Utils\Rules::ruleInfoById($results);
    }

    public function getAllRulesWithOperatorValues(int $apiKey): array {
        $params = [
            ':api_key' => $apiKey,
        ];

        $query = (
            'SELECT
                dshb_rules.id,
                COALESCE(dshb_operators_rules.value, 0) AS value

            FROM
                dshb_rules

            LEFT JOIN dshb_operators_rules
            ON (dshb_rules.id = dshb_operators_rules.rule_id AND dshb_operators_rules.key = :api_key)'
        );

        $results = $this->execQuery($query, $params);

        // attribues filter applied in controller
        return \Utils\Rules::ruleInfoById($results);
    }

    public function updateRule(int $ruleId, int $score, int $apiKey): void {
        $found = $this->load(
            ['"key"=? AND "rule_id"=?', $apiKey, $ruleId],
        );

        if (!$found) {
            $this->key = $apiKey;
            $this->rule_id = $ruleId;
            $this->proportion = null;
        }

        $this->value = $score;
        // do not change proportion

        $this->save();
    }

    public function updateRuleProportion(int $ruleId, float $proportion, int $apiKey): void {
        $found = $this->load(
            ['"key"=? AND "rule_id"=?', $apiKey, $ruleId],
        );

        // set value if record is new
        if (!$found) {
            $this->key = $apiKey;
            $this->rule_id = $ruleId;
            $this->value = 0;
        }

        $this->proportion = $proportion;
        $this->proportion_updated_at = date('Y-m-d H:i:s');

        $this->save();
    }
}
