<?php
require_once 'includes/auth.php';
logout_user();
flash('Sesión cerrada');
redirect('index.php');