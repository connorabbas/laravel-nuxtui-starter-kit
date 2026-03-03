<?php

namespace App\Enums;

enum FilterMatchMode: string
{
    case STARTS_WITH = 'startsWith';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'notContains';
    case ENDS_WITH = 'endsWith';
    case EQUALS = 'equals';
    case NOT_EQUALS = 'notEquals';
    case IN = 'in';
    case LESS_THAN = 'lt';
    case LESS_THAN_OR_EQUAL_TO = 'lte';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_OR_EQUAL_TO = 'gte';
    case BETWEEN = 'between';
    case DATE_IS = 'dateIs';
    case DATE_IS_NOT = 'dateIsNot';
    case DATE_BEFORE = 'dateBefore';
    case DATE_AFTER = 'dateAfter';

    public function label(): string
    {
        return match ($this) {
            self::STARTS_WITH => __('Starts with'),
            self::CONTAINS => __('Contains'),
            self::NOT_CONTAINS => __('Does not contain'),
            self::ENDS_WITH => __('Ends with'),
            self::EQUALS => __('Equals'),
            self::NOT_EQUALS => __('Does not equal'),
            self::IN => __('In list'),
            self::LESS_THAN => __('Less than'),
            self::LESS_THAN_OR_EQUAL_TO => __('Less than or equal'),
            self::GREATER_THAN => __('Greater than'),
            self::GREATER_THAN_OR_EQUAL_TO => __('Greater than or equal'),
            self::BETWEEN => __('Between'),
            self::DATE_IS => __('Date is'),
            self::DATE_IS_NOT => __('Date is not'),
            self::DATE_BEFORE => __('Date before'),
            self::DATE_AFTER => __('Date after'),
        };
    }
}
