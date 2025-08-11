<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // =====================
    // ÁREA ADMINISTRATIVA
    // =====================
    case VIEW_ADMIN = 'admin-view';

    // =====================
    // USUÁRIOS
    // =====================
    case CREATE_USER = 'user-create';
    case UPDATE_USER = 'user-update';
    case DELETE_USER = 'user-delete';
    case VIEW_USER = 'user-view';
    case RESET_USER_PASSWORD = 'user-reset-password';

    // =====================
    // CLIENTES
    // =====================
    case CREATE_CLIENT = 'client-create';
    case UPDATE_CLIENT = 'client-update';
    case DELETE_CLIENT = 'client-delete';
    case VIEW_CLIENT = 'client-view';

    // =====================
    // ORDENS DE SERVIÇO
    // =====================
    case CREATE_ORDER = 'order-create';
    case UPDATE_ORDER = 'order-update';
    case DELETE_ORDER = 'order-delete';
    case VIEW_ORDER = 'order-view';
    case CLOSE_ORDER = 'order-close';
    case APPROVE_ORDER = 'order-approve';
    case CANCEL_ORDER = 'order-cancel';

    // =====================
    // PRODUTOS / SERVIÇOS
    // =====================
    case CREATE_PRODUCT = 'product-create';
    case UPDATE_PRODUCT = 'product-update';
    case DELETE_PRODUCT = 'product-delete';
    case VIEW_PRODUCT = 'product-view';

    case CREATE_SERVICE = 'service-create';
    case UPDATE_SERVICE = 'service-update';
    case DELETE_SERVICE = 'service-delete';
    case VIEW_SERVICE = 'service-view';

    // =====================
    // RELATÓRIOS
    // =====================
    case VIEW_REPORT_FINANCIAL = 'report-financial-view';
    case VIEW_REPORT_ORDERS = 'report-orders-view';
    case VIEW_REPORT_CLIENTS = 'report-clients-view';
    case EXPORT_REPORT = 'report-export';

    // =====================
    // CONFIGURAÇÕES
    // =====================
    case UPDATE_SETTINGS_SYSTEM = 'settings-system-update';
    case UPDATE_SETTINGS_NOTIFICATIONS = 'settings-notifications-update';
}
