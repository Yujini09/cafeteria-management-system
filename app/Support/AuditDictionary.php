<?php

namespace App\Support;

final class AuditDictionary
{
    // Modules
    public const MODULE_AUTH = 'auth';
    public const MODULE_PROFILE = 'profile';
    public const MODULE_USERS = 'users';
    public const MODULE_MESSAGES = 'messages';
    public const MODULE_RESERVATIONS = 'reservations';
    public const MODULE_PAYMENTS = 'payments';
    public const MODULE_MENUS = 'menus';
    public const MODULE_INVENTORY = 'inventory';
    public const MODULE_RECIPES = 'recipes';
    public const MODULE_REPORTS = 'reports';

    // Auth
    public const LOGGED_IN = 'Logged In';
    public const LOGGED_OUT = 'Logged Out';
    public const FAILED_LOGIN = 'Failed Login';
    public const REQUESTED_PASSWORD_RESET = 'Requested Password Reset';
    public const COMPLETED_PASSWORD_RESET = 'Completed Password Reset';
    public const VERIFIED_EMAIL = 'Verified Email';
    public const LOGGED_IN_VIA_GOOGLE = 'Logged In via Google';

    // Profile
    public const UPDATED_PROFILE = 'Updated Profile';
    public const UPDATED_PASSWORD = 'Updated Password';
    public const UPDATED_AVATAR = 'Updated Avatar';

    // Users
    public const CREATED_ADMIN_USER = 'Created Admin User';
    public const UPDATED_ADMIN_USER = 'Updated Admin User';
    public const DELETED_USER = 'Deleted User';

    // Messages
    public const SUBMITTED_MESSAGE = 'Submitted Message';
    public const VIEWED_MESSAGE = 'Viewed Message';
    public const REPLIED_TO_MESSAGE = 'Replied to Message';
    public const DELETED_MESSAGE = 'Deleted Message';

    // Reservations
    public const CREATED_RESERVATION = 'Created Reservation';
    public const UPDATED_RESERVATION = 'Updated Reservation';
    public const APPROVED_RESERVATION = 'Approved Reservation';
    public const DECLINED_RESERVATION = 'Declined Reservation';
    public const CANCELLED_RESERVATION = 'Cancelled Reservation';
    public const UPLOADED_RECEIPT = 'Uploaded Receipt';
    public const ADDED_ADDITIONAL_CHARGE = 'Added Additional Charge';
    public const UPDATED_ADDITIONAL_CHARGE = 'Updated Additional Charge';
    public const DELETED_ADDITIONAL_CHARGE = 'Deleted Additional Charge';

    // Payments
    public const SUBMITTED_PAYMENT = 'Submitted Payment';
    public const APPROVED_PAYMENT = 'Approved Payment';
    public const REJECTED_PAYMENT = 'Rejected Payment';

    // Menus
    public const CREATED_MENU = 'Created Menu';
    public const UPDATED_MENU = 'Updated Menu';
    public const DELETED_MENU = 'Deleted Menu';
    public const UPDATED_MENU_PRICES = 'Updated Menu Prices';

    // Inventory
    public const ADDED_INVENTORY_ITEM = 'Added Inventory Item';
    public const UPDATED_INVENTORY_ITEM = 'Updated Inventory Item';
    public const DELETED_INVENTORY_ITEM = 'Deleted Inventory Item';

    // Recipes
    public const ADDED_RECIPE_INGREDIENT = 'Added Recipe Ingredient';
    public const UPDATED_RECIPE_INGREDIENT = 'Updated Recipe Ingredient';
    public const REMOVED_RECIPE_INGREDIENT = 'Removed Recipe Ingredient';

    // Reports
    public const GENERATED_REPORT = 'Generated Report';
    public const EXPORTED_REPORT_PDF = 'Exported Report PDF';
    public const EXPORTED_REPORT_EXCEL = 'Exported Report Excel';
}
