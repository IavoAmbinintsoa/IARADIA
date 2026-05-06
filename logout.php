<?php
require_once __DIR__ . '/inc/functions.php';
logout_user();
redirect('/login.php');