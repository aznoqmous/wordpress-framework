<?php

namespace Addictic\WordpressFramework\Command;

use Addictic\WordpressFramework\Annotation\Command;
use Addictic\WordpressFramework\Helpers\AssetsHelper;
use Addictic\WordpressFramework\Models\Legacy\UserModel;
use Addictic\WordpressFramework\WordpressFrameworkBundle;

/**
 * @Command(name="user:password")
 */
class UserPasswordCommand
{
    public function __invoke($username, $password)
    {
        $user = UserModel::findOneBy([
            "user_login = \"$username\""
        ]);
        if(!$user) throw new \Exception("User not found\n");
        $user->user_pass = password_hash($password, PASSWORD_DEFAULT);
        $user->save();
        echo "User \"$username\" password changed.";
    }
}

