<!--
// *******************************************************
// 
// VariaPrint 1.0 web-to-print system
//
// Copyright 2001-2014 Luke Miller
//
// This file is part of VariaPrint, a web-to-print PDF personalization and 
// ordering system.
// 
// VariaPrint is free software: you can redistribute it and/or modify it under 
// the terms of the GNU General Public License as published by the Free Software 
// Foundation, either version 2 of the License, or (at your option) any later 
// version.
// 
// VariaPrint is distributed in the hope that it will be useful, but WITHOUT ANY 
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
// A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License along with 
// VariaPrint. If not, see http://www.gnu.org/licenses/.
// 
//
// Forking, porting, updating, and contributing back to this project is welcomed.
// 
// If you find any of this useful, let me know at the address below...
//
// https://github.com/lukedmiller/variaprint
//
// http://www.variaprint.com/
//
// *******************************************************
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Help: Approving orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
<p class="titlebold">Approving/canceling orders</p>
<p class="text">There are two ways to  approve orders. (See <a href="users-approval.php" target="mainFrame">Setting
up approval managers</a> for more information)</p>
<ul>
  <li class="text">As a manager that has access to the Approve Orders	section
    of the VariaPrint&#8482; manager <br>
    - Requires
that the person approving has a manager account<br>
- Any ordered item can be approved by any manager with approval access</li>
  <li class="text">As a manager whose email is listed in the checkout approval
    selection. <br>
    - Validation is done by sending the manager an encrypted URL link in an email
      that they click on to view orders <br>
      - Manager can only see items in orders that their email address was selected
      to view</li>
</ul>
<p class="text">For the first method, simply go to the Approve section under
  Orders in the VariaPrint&#8482; manager and approve / cancel / or send a note to
  the buyer.</p>
<p class="text">For method two, simply use the link in the email that is sent.</p>
<p class="text">Both methods send a message to the buyer whether the order is
  approved or canceled. The message is customizable by the manager sending the
  message.</p>
</body>
</html>
