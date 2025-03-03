# API Documentation

This document outlines the available API endpoints for the application, along with their functionalities, required parameters, and expected responses.

## Table of Contents

1. [Authentication](#authentication)
2. [User Management](#user-management)
3. [Vendor Management](#vendor-management)
4. [Category Management](#category-management)
5. [Product Management](#product-management)
6. [Product Details Management](#product-details-management)
7. [Favorites Management](#favorites-management)
8. [Promotion Management](#promotion-management)
9. [Review Management](#review-management)
10. [Home Page Data](#home-page-data)
11. [Notification Management](#notification-management)

---

## Authentication

### 1. Register
- **Endpoint**: `POST /register`
- **Description**: Register a new user.
- **Required Parameters**:
  - `name`: User's name (string, max: 255).
  - `email`: User's email (string, unique).
  - `password`: User's password (string, min: 8, confirmed).
  - `gender`: User's gender (`male`, `female`, `other`).
  - `profile_picture` (optional): User's profile picture (image, formats: jpeg, png, jpg, gif, svg, max size: 2MB).

### 2. Login
- **Endpoint**: `POST /login`
- **Description**: Authenticate a user and generate an access token.
- **Required Parameters**:
  - `email`: User's email (string).
  - `password`: User's password (string).

### 3. Logout
- **Endpoint**: `POST /logout`
- **Description**: Invalidate the current user's access token.
- **Authorization**: Requires authentication.

---

## User Management

### 1. Get All Users
- **Endpoint**: `GET /users`
- **Description**: Retrieve all users (admin-only).
- **Optional Parameters**:
  - `search`: Search by name or email.
  - `role`: Filter by user role (`user`, `vendor`, `admin`).
  - `is_active`: Filter by active status (`true` or `false`).
  - `sort_by`: Sort by field (default: `created_at`).
  - `sort_order`: Sort order (`asc` or `desc`).

### 2. Get User Profile
- **Endpoint**: `GET /user`
- **Description**: Retrieve the authenticated user's profile.
- **Authorization**: Requires authentication.

### 3. Update User Profile
- **Endpoint**: `POST /user`
- **Description**: Update the authenticated user's profile.
- **Optional Parameters**:
  - `name`: User's name (string, max: 255).
  - `email`: User's email (string, unique).
  - `password`: User's password (string, min: 8, confirmed).
  - `address`: User's address (string, max: 255).
  - `phone`: User's phone number (string, unique).
  - `gender`: User's gender (`male`, `female`, `other`).
  - `profile_picture` (optional): User's profile picture (image, formats: jpeg, png, jpg, gif, svg, max size: 2MB).

### 4. Delete User
- **Endpoint**: `DELETE /user/{id}`
- **Description**: Delete a user (admin-only or self-deletion).
- **Authorization**: Requires authentication.

---

## Vendor Management

### 1. Get All Vendors
- **Endpoint**: `GET /vendor`
- **Description**: Retrieve all vendors (admin-only).
- **Optional Parameters**:
  - `search`: Search by brand name or description.
  - `status`: Filter by vendor status (`active`, `inactive`, `pending`).

### 2. Get Public Vendors
- **Endpoint**: `GET /vendor`
- **Description**: Retrieve all active vendors (public access).

### 3. Create Vendor
- **Endpoint**: `POST /vendor`
- **Description**: Create a new vendor (admin-only).
- **Required Parameters**:
  - `brand_name`: Vendor's brand name (string, unique).
  - `description`: Vendor's description (string).
  - `phone`: Vendor's phone number (string, unique).
  - `vendor_images`: Vendor's image (image, formats: jpeg, png, jpg, gif, max size: 2MB).
  - `regions`: Array of regions associated with the vendor.
    - `region_id`: Region ID (integer, exists in `regions` table).
    - `delivery_cost`: Delivery cost (numeric, optional).
    - `discount`: Discount percentage (integer, optional).
    - `description`: Description of the region association (string, optional).

### 4. Approve/Reject Vendor
- **Endpoints**:
  - `POST /vendor/{vendor}/approve`
  - `POST /vendor/{vendor}/reject`
- **Description**: Approve or reject a vendor's registration (admin-only).

### 5. Soft Delete Vendor
- **Endpoint**: `DELETE /vendor/{vendor}`
- **Description**: Soft delete a vendor (admin-only).

### 6. Restore Deleted Vendor
- **Endpoint**: `POST /vendor/{id}/restore`
- **Description**: Restore a soft-deleted vendor (admin-only).

### 7. Show a Single Vendor
- **Endpoint**: `GET /vendor/{vendor}`
- **Description**: Retrieve details of a specific vendor.
- **Authorization**: Requires authentication.
- **Response**:
  - `id`: Vendor ID.
  - `brand_name`: Vendor's brand name.
  - `description`: Vendor's description.
  - `phone`: Vendor's phone number.
  - `status`: Vendor's status (`active`, `inactive`, `pending`).
  - `regions`: Array of regions associated with the vendor.
    - `region_id`: Region ID.
    - `delivery_cost`: Delivery cost for the region.
    - `discount`: Discount percentage for the region.
    - `description`: Description of the region association.
  - `users`: Array of users associated with the vendor.

---

### 8. Update Vendor
- **Endpoint**: `PUT /vendor/{vendor}`
- **Description**: Update an existing vendor's information (admin-only or self-update for the vendor's owner).
- **Required Parameters**:
  - `brand_name`: Vendor's updated brand name (string, unique).
  - `description`: Vendor's updated description (string).
  - `phone`: Vendor's updated phone number (string, unique).
- **Optional Parameters**:
  - `vendor_images`: Updated vendor image (image, formats: jpeg, png, jpg, gif, max size: 2MB).
  - `regions`: Array of regions associated with the vendor.
    - `region_id`: Region ID (integer, exists in `regions` table).
    - `delivery_cost`: Updated delivery cost (numeric, optional).
    - `discount`: Updated discount percentage (integer, optional).
    - `description`: Updated description of the region association (string, optional).

---

### 9. Get Soft-Deleted Vendors
- **Endpoint**: `GET /vendor/trashed`
- **Description**: Retrieve all soft-deleted vendors (admin-only).
- **Optional Parameters**:
  - `search`: Search by brand name or description.
- **Response**:
  - `id`: Vendor ID.
  - `brand_name`: Vendor's brand name.
  - `description`: Vendor's description.
  - `phone`: Vendor's phone number.
  - `deleted_at`: Timestamp of when the vendor was soft-deleted.
  - `regions`: Array of regions associated with the vendor before deletion.
  - `users`: Array of users associated with the vendor before deletion.

---

### 10. Restore a Soft-Deleted Vendor
- **Endpoint**: `POST /vendor/{id}/restore`
- **Description**: Restore a soft-deleted vendor (admin-only).
- **Response**:
  - `id`: Restored vendor ID.
  - `brand_name`: Restored vendor's brand name.
  - `description`: Restored vendor's description.
  - `phone`: Restored vendor's phone number.
  - `status`: Restored vendor's status (`active` by default after restoration).

---

### 11. Dashboard Vendors
- **Endpoint**: `GET /dashboard/vendor`
- **Description**: Retrieve all vendors for the admin dashboard (admin-only).
- **Optional Parameters**:
  - `search`: Search by brand name or description.
  - `status`: Filter by vendor status (`active`, `inactive`, `pending`).
- **Response**:
  - `id`: Vendor ID.
  - `brand_name`: Vendor's brand name.
  - `description`: Vendor's description.
  - `phone`: Vendor's phone number.
  - `status`: Vendor's status.
  - `regions`: Array of regions associated with the vendor.
  - `users`: Array of users associated with the vendor.

---

## Category Management

### 1. Get All Categories
- **Endpoint**: `GET /category`
- **Description**: Retrieve all categories (admin-only).
- **Optional Parameters**:
  - `search`: Search by category name or description.

### 2. Create Category
- **Endpoint**: `POST /category`
- **Description**: Create a new category (admin-only).
- **Required Parameters**:
  - `category_name`: Category's name (string, unique).
  - `description`: Category's description (string, optional).
  - `image` (optional): Category's image (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 3. Update Category
- **Endpoint**: `PUT /category/{category}`
- **Description**: Update an existing category (admin-only).
- **Optional Parameters**:
  - `category_name`: Category's name (string, unique).
  - `description`: Category's description (string, optional).
  - `image` (optional): Category's image (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 4. Delete Category
- **Endpoint**: `DELETE /category/{category}`
- **Description**: Delete a category (admin-only).

---

## Product Management

### 1. Get Products
- **Endpoint**: `GET /product`
- **Description**: Retrieve products with filtering and sorting options.
- **Optional Parameters**:
  - `vendor_id`: Filter by vendor ID.
  - `category_id`: Filter by category ID.
  - `search`: Search by product name.
  - `price_range`: Filter by price range (format: `min,max`).
  - `sort_by`: Sort by field (`latest`, `lowest_price`, `highest_price`, `most_demanded`).

### 2. Create Product
- **Endpoint**: `POST /product`
- **Description**: Create a new product (vendor-only).
- **Required Parameters**:
  - `product_name`: Product's name (string, unique).
  - `description`: Product's description (string, optional).
  - `categories`: Array of category IDs (integer, exists in `categories` table).
  - `details`: Array of product details.
    - `size` (optional): Size of the product (string).
    - `color` (optional): Color of the product (string).
    - `price`: Price of the product (numeric, min: 0).
    - `discount` (optional): Discount percentage (numeric, min: 0).
    - `stock`: Stock quantity (integer, min: 0).
    - `material` (optional): Material description (string).
    - `images.*`: Product images (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 3. Update Product
- **Endpoint**: `PUT /product/{product}`
- **Description**: Update an existing product (vendor-only).
- **Optional Parameters**:
  - `product_name`: Product's name (string, unique).
  - `description`: Product's description (string, optional).
  - `categories`: Array of category IDs (integer, exists in `categories` table).
  - `details`: Array of product details.
    - `id`: Detail ID (integer, exists in `product_details` table).
    - `size` (optional): Size of the product (string).
    - `color` (optional): Color of the product (string).
    - `price`: Price of the product (numeric, min: 0).
    - `discount` (optional): Discount percentage (numeric, min: 0).
    - `stock`: Stock quantity (integer, min: 0).
    - `material` (optional): Material description (string).
    - `images.*`: Product images (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 4. Delete Product
- **Endpoint**: `DELETE /product/{product}`
- **Description**: Delete a product (vendor-only).

---

## Product Details Management

### 1. Get Product Details
- **Endpoint**: `GET /product/{product}/details`
- **Description**: Retrieve product details for a specific product.
- **Optional Parameters**:
  - `search`: Search by detail attributes.

### 2. Create Product Detail
- **Endpoint**: `POST /product/{product}/details`
- **Description**: Create a new product detail (vendor-only).
- **Required Parameters**:
  - `size` (optional): Size of the product (string).
  - `color` (optional): Color of the product (string).
  - `price`: Price of the product (numeric, min: 0).
  - `discount` (optional): Discount percentage (numeric, min: 0).
  - `stock`: Stock quantity (integer, min: 0).
  - `material` (optional): Material description (string).
  - `images.*`: Product images (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 3. Update Product Detail
- **Endpoint**: `PUT /product/{product}/details/{detail}`
- **Description**: Update an existing product detail (vendor-only).
- **Optional Parameters**:
  - `size`: Size of the product (string).
  - `color`: Color of the product (string).
  - `price`: Price of the product (numeric, min: 0).
  - `discount`: Discount percentage (numeric, min: 0).
  - `stock`: Stock quantity (integer, min: 0).
  - `material`: Material description (string).
  - `images.*`: Product images (image, formats: jpeg, png, jpg, gif, max size: 2MB).

### 4. Delete Product Detail
- **Endpoint**: `DELETE /product/{product}/details/{detail}`
- **Description**: Delete a product detail (vendor-only).

---

## Favorites Management

### 1. Add to Favorites
- **Endpoint**: `POST /favorite/add/{product}`
- **Description**: Add a product to the authenticated user's favorites.
- **Authorization**: Requires authentication.

### 2. Remove from Favorites
- **Endpoint**: `DELETE /favorite/remove/{product}`
- **Description**: Remove a product from the authenticated user's favorites.
- **Authorization**: Requires authentication.

### 3. Get Favorite Products
- **Endpoint**: `GET /favorite`
- **Description**: Retrieve the authenticated user's favorite products.
- **Optional Parameters**:
  - `search`: Search by product name.
  - `price_range`: Filter by price range (format: `min,max`).
  - `sort_by`: Sort by field (`latest`, `lowest_price`, `highest_price`).

### 4. Check Favorite Status
- **Endpoint**: `GET /favorite/check/{product}`
- **Description**: Check if a product is in the authenticated user's favorites.
- **Authorization**: Requires authentication.

---

## Promotion Management

### 1. Get Promotions
- **Endpoint**: `GET /promotion`
- **Description**: Retrieve all promotions (admin-only).

### 2. Create Promotion
- **Endpoint**: `POST /promotion`
- **Description**: Create a new promotion (admin-only).
- **Required Parameters**:
  - `name`: Promotion's name (string).
  - `promotion_amount`: Promotion amount (numeric, min: 0).
  - `promotion_priority`: Promotion priority (integer, min: 0).
  - `duration`: Promotion duration in days (integer, min: 1).
  - `status`: Promotion status (`active` or `inactive`).

### 3. Subscribe to Promotion
- **Endpoint**: `POST /vendors/{vendor}/promotion/{promotion}/subscribe`
- **Description**: Subscribe a vendor to a promotion (vendor-only).
- **Required Parameters**:
  - `start_date`: Start date of the subscription (date, after or equal to today).

### 4. Approve/Reject Subscription
- **Endpoints**:
  - `PUT /vendors/{vendor}/promotion/{promotion}/approve`
  - `PUT /vendors/{vendor}/promotion/{promotion}/reject`
- **Description**: Approve or reject a vendor's subscription to a promotion (admin-only).

### 5. Update Promotion
- **Endpoint**: `PUT /promotion/{promotion}`
- **Description**: Update an existing promotion (admin-only).
- **Optional Parameters**:
  - `name`: Promotion's updated name (string).
  - `promotion_amount`: Updated promotion amount (numeric, min: 0).
  - `promotion_priority`: Updated promotion priority (integer, min: 0).
  - `duration`: Updated promotion duration in days (integer, min: 1).
  - `status`: Updated promotion status (`active` or `inactive`).

### 6. Get Promoted Products
- **Endpoint**: `GET /promotion/promoted-products`
- **Description**: Retrieve all products that are currently promoted.
- **Optional Parameters**:
  - `search`: Search by product name.
  - `price_range`: Filter by price range (format: `min,max`).
  - `sort_by`: Sort by field (`latest`, `lowest_price`, `highest_price`).

### 7. Get Most Demanded Products
- **Endpoint**: `GET /promotion/most-demanded-products`
- **Description**: Retrieve the most demanded products, including those currently promoted.
- **Optional Parameters**:
  - `search`: Search by product name.
  - `price_range`: Filter by price range (format: `min,max`).
  - `sort_by`: Sort by field (`latest`, `lowest_price`, `highest_price`).

---

## Review Management

### 1. Get Reviews
- **Endpoint**: `GET /product/{product}/reviews`
- **Description**: Retrieve reviews for a specific product.
- **Optional Parameters**:
  - `search`: Search by review comment.

### 2. Create Review
- **Endpoint**: `POST /product/{product}/reviews`
- **Description**: Create a new review for a product (authenticated user only).
- **Required Parameters**:
  - `rating`: Rating value (integer, min: 1, max: 5).
  - `comment` (optional): Review comment (string, max: 1000).

### 3. Update Review
- **Endpoint**: `PUT /product/{product}/reviews/{review}`
- **Description**: Update an existing review (owner-only).
- **Optional Parameters**:
  - `rating`: Rating value (integer, min: 1, max: 5).
  - `comment`: Review comment (string, max: 1000).

### 4. Delete Review
- **Endpoint**: `DELETE /product/{product}/reviews/{review}`
- **Description**: Delete a review (owner-only).

---

## Home Page Data

### 1. Get Home Data
- **Endpoint**: `GET /home`
- **Description**: Retrieve data for the home page (public access).
- **Returned Data**:
  - `categories`: Important categories with 4 products each.
  - `promoted_products`: Products currently promoted.
  - `followed_vendors`: Latest products from followed vendors.
  - `demanded_products`: Most demanded products.

---

## Notification Management

### 1. Get Notifications
- **Endpoint**: `GET /notifications`
- **Description**: Retrieve notifications for the authenticated user.
- **Pagination**:
  - `per_page`: Number of notifications per page (default: 10).

### 2. Mark Notification as Read
- **Endpoint**: `PUT /notifications/{id}/read`
- **Description**: Mark a specific notification as read.

### 3. Mark All Notifications as Read
- **Endpoint**: `PUT /notifications/read-all`
- **Description**: Mark all notifications as read.

---

## Additional Notes

- **Authentication**: Most endpoints require authentication using Sanctum. Include the `Authorization: Bearer <token>` header in your requests.
- **Caching**: Some endpoints use caching to improve performance. Cache is invalidated upon data modification.
- **Error Handling**: Standard error responses include:
  - `400 Bad Request`: Invalid input or constraints.
  - `401 Unauthorized`: Missing or invalid authentication token.
  - `403 Forbidden`: Insufficient permissions.
  - `404 Not Found`: Resource not found.
  - `500 Internal Server Error`: Unexpected server-side errors.

