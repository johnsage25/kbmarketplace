INSERT INTO `_PREFIX_kb_mp_email_template` (`end`, `name`, `description`, `date_add`, `date_upd`) VALUES
('f', 'mp_welcome_seller', 'This template is used when a new customer registers as seller on the store. Note that the account is not yet approved due to which customer/seller has only limited access to the seller account.', NOW(), NOW()),
('f', 'mp_seller_account_approval', 'This template is used to notify the customer/seller about the approval of the seller account.', '2015-08-07 00:00:00', '2015-08-07 00:00:00'),
('f', 'mp_seller_account_disapproval', 'This template is used to notify the customer/seller about the disapproval of the seller account request. The customer/seller can request for the account once again.', NOW(), NOW()),
('b', 'mp_seller_registration_notification_admin', 'This template is used to notify the admin about the new registration of a customer as seller. The admin has to approve the account request.', NOW(), NOW()),
('f', 'mp_seller_account_approval_after_disapprove', 'This template is used to notify the admin about request for approving account of a customer as seller after after disapproving by admin.', NOW(), NOW()),
('b', 'mp_new_product_notification_admin', 'This template is used to notify the admin about the addition if a new product into the store by the respective seller. The admin needs to approve the product to make it visible in front.', NOW(), NOW()),
('b', 'mp_category_request_notification_admin', 'This template is used to notify the admin about a new category request by the respective customer.', NOW(), NOW()),
('f', 'mp_category_request_approved', 'This template is used to notify the seller about the approval of the category requested by the seller.', NOW(), NOW()),
('f', 'mp_category_request_disapproved', 'This template is used to notify the seller about the disapproval of the category requested by the seller.', NOW(), NOW()),
('f', 'mp_product_disapproval_notification', 'This template is used to notify the seller about the disapproval of the product added by the seller.', NOW(), NOW()),
('f', 'mp_product_approval_notification', 'This template is used to notify the seller about the approval of the product added by the seller.', NOW(), NOW()),
('f', 'mp_product_delete_notification', 'This template is used to notify the seller about the deletion of the product added by the seller.', NOW(), NOW()),
('b', 'mp_seller_review_approval_request_admin', 'This template is used to notify the admin about the new review posted on any seller. The admin has to approve the review to make it visible at front.', NOW(), NOW()),
('f', 'mp_seller_review_notification', 'This template is used to notify the seller about the new review posted on the seller itself. The admin has to approve the review to make it visible at front.', NOW(), NOW()),
('f', 'mp_seller_amount_credit_transfer_notification', 'This template is used to notify the seller about the new transaction made by the admin for the earning of the seller. This template contains all the details of the transaction.', NOW(), NOW()),
('f', 'mp_seller_review_approved_to_customer', 'This template is used to notify the customer for his review approved by admin and listed on store', NOW(), NOW()),
('f', 'mp_seller_review_approved_to_seller', 'This template is used to notify seller for review given by customer, approved by admin and listed on store', NOW(), NOW()),
('f', 'mp_seller_review_disspproved_to_seller', 'This template is used to notify seller for review disapproved by admin, given by customer.', NOW(), NOW()),
('f', 'mp_seller_review_disspproved_to_customer', 'This template is used to notify customer for review disapproved by admin, given by you on store.', NOW(), NOW()),
('f', 'mp_seller_amount_debit_transfer_notification', 'This template is used to notify the seller about the new transaction made by the admin for debited some amount from the current balance amount of the seller. This template contains all the details of the transaction.', NOW(), NOW()),
('b','mp_seller_review_delete_to_seller', 'This template is used to notify the seller about review given by customer deleted by admin.', NOW(), NOW()),
('f','mp_seller_review_delete_to_customer', 'This template is used to notify the customer about the deletion of review given by you', NOW(), NOW()),
('f','mp_seller_account_enable', 'This template is used to notify the seller about the enable of seller account.', NOW(), NOW()),
('f','mp_seller_account_disable', 'This template is used to notify the seller about the disable of seller account.', NOW(), NOW());

INSERT INTO `_PREFIX_kb_mp_seller_menu` (`module_name`, `controller_name`, `position`, `icon`, `css_class`, `show_badge`, `badge_class`, `date_add`, `date_upd`) VALUES
('kbmarketplace', 'dashboard', 1, '&#xe871;', NULL, 0, NULL, NOW(), NOW()),
('kbmarketplace', 'seller', 2, '&#xe0ba;', NULL, 0, NULL, NOW(), NOW()),
('kbmarketplace', 'product', 3, '&#xe8ef;', NULL, 1, 'KbSellerProduct', NOW(), NOW()),
('kbmarketplace', 'order', 4, '&#xe85d;', NULL, 1, 'KbSellerEarning', NOW(), NOW()),
('kbmarketplace', 'productreview', 5, '&#xe24c;', NULL, 1, 'KbSellerProductReview', NOW(), NOW()),
('kbmarketplace', 'sellerreview', 6, '&#xe87d;', NULL, 1, 'KbSellerReview', NOW(), NOW()),
('kbmarketplace', 'earning', 7, '&#xe53e;', NULL, 0, NULL, NOW(), NOW()),
('kbmarketplace', 'transaction', 8, '&#xe850;', NULL, 0, NULL, NOW(), NOW()),
('kbmarketplace', 'category', 9, '&#xe41d;', NULL, 1, 'KbSellerCRequest', NOW(), NOW()),
('kbmarketplace', 'shipping', 10, '&#xe532;', NULL, 1, 'KbSellerShipping', NOW(), NOW());
