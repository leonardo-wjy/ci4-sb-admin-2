<?php
if (!function_exists('is_login')) {
   function is_login()
   {
      return session()->get("login");
   }
}