<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%currency_rates}}`.
 */
class m250507_230810_create_currency_rates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('currency_rates', [
            'id' => $this->primaryKey(),
            'code' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'rate' => $this->decimal(10, 4)->notNull(),
            'date' => $this->date()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-currency_rates-code-date', 'currency_rates', ['code', 'date'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('currency_rates');
    }
}
