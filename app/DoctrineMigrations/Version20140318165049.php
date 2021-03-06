<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140318165049 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->addSql("DROP TABLE user_level");
        $this->addSql("
        	DROP TABLE IF EXISTS `coupon`;
			CREATE TABLE `coupon` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `code` varchar(255) NOT NULL COMMENT '优惠码',
			  `type` enum('minus','discount') NOT NULL COMMENT '优惠方式',
			  `status` enum('used','unused') NOT NULL COMMENT '使用状态',
			  `rate` float(10,2) unsigned NOT NULL COMMENT '若优惠方式为打折，则为打折率，若为抵价，则为抵价金额',
			  `batchId` int(10) unsigned NOT NULL COMMENT '批次号',
			  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用者',
			  `deadline` int(10) unsigned NOT NULL COMMENT '失效时间',
			  `targetType` varchar(64) NOT NULL COMMENT '使用对象类型',
			  `targetId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用对象',
			  `orderId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单号',
			  `orderTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
			  `createdTime` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='优惠码表';

        	CREATE TABLE `coupon_batch` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) NOT NULL COMMENT '批次名称',
			  `type` enum('minus','discount') NOT NULL COMMENT '优惠方式',
			  `generatedNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '生成数',
			  `usedNum` int(11) NOT NULL DEFAULT '0' COMMENT '使用次数',
			  `rate` float(10,2) unsigned NOT NULL COMMENT '若优惠方式为打折，则为打折率，若为抵价，则为抵价金额',
			  `prefix` varchar(64) NOT NULL COMMENT '批次前缀',
			  `digits` int(20) unsigned NOT NULL COMMENT '优惠码位数',
			  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '已优惠金额',
			  `deadline` int(10) unsigned NOT NULL COMMENT '失效时间',
			  `targetType` varchar(64) NOT NULL DEFAULT '' COMMENT '使用对象类型',
			  `targetId` int(10) unsigned NOT NULL DEFAULT '0',
			  `description` text COMMENT '优惠说明',
			  `createdTime` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='优惠码批次表';

			CREATE TABLE `member_level` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `seq` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
			  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '会员类型名称',
			  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '示意图标',
			  `picture` varchar(255) NOT NULL DEFAULT '' COMMENT '展示图片',
			  `monthPrice` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '月费价格',
			  `yearPrice` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '年费价格',
			  `description` text COMMENT '一句话描述',
			  `freeLearned` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费学习课程',
			  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
			  `createdTime` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='会员类型表';

			CREATE TABLE `member_history` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `userId` int(10) unsigned NOT NULL COMMENT '购买用户',
			  `userNickname` varchar(64) NOT NULL DEFAULT '',
			  `levelId` int(10) unsigned NOT NULL COMMENT '会员类型',
			  `deadline` int(10) unsigned NOT NULL,
			  `boughtType` enum('new','upgrade','renew','edit') NOT NULL COMMENT '购买类型',
			  `boughtTime` int(10) unsigned NOT NULL,
			  `boughtDuration` int(10) unsigned NOT NULL,
			  `boughtPrice` float(10,2) unsigned NOT NULL COMMENT '购买价格',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员记录表';
        	");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
