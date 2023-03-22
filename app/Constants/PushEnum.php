<?php

namespace App\Constants;

class PushEnum
{
    const REQUEST_CREATED ='request_created';
    const TRIP_ACCEPTED_BY_DRIVER = 'trip_accepted';
    const LOCAL_TO_RENTAL = 'local_to_rental';
    const NO_DRIVER_FOUND ='no_driver_found';
    const REQUEST_CANCELLED_BY_USER = 'request_cancelled_by_user';
    const REQUEST_CANCELLED_BY_DRIVER ='request_cancelled_by_driver';
    const REQUEST_CANCELLED_BY_DISPATCHER ='request_cancelled_by_dispatcher';

    const DRIVER_ARRIVED = 'driver_arrived';
    const DRIVER_STARTED_THE_TRIP = 'driver_started_the_trip';
    const DRIVER_END_THE_TRIP = 'driver_end_the_trip';

    const DRIVER_APPROVED = 'driver_approved';
    const DRIVER_BLOCKED = 'driver_blocked';

    const SILENT_PUSH = 'silent_push';
    const LOGOUT_PUSH = 'logout_push';

    const PASSENGER_UPLOAD_IMAGES = 'passenger_upload_images';
    const UPLOAD_IMAGE_SUCCESS = 'upload_image_success';
    const UPLOAD_IMAGE_RETAKE = 'upload_image_retake';
    const KILOMETER_START ='request_created';
    const NEW_PROMO_CODE ='new_promo_code';
    const USER_PAYMENT_CHANGE = 'user_payment_change';
    const PAYMENT_CHANGE = 'payment_change';
    const USER_PAYMENT_DONE = 'user_payment_done';
    const PAYMENT_DONE = 'payment_done';
    const ALREADY_DRIVER_ACCEPT_TRIP = 'already_driver_accept_trip';



}