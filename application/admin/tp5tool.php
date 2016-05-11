<?php

return [
    /**
     * 用户模型
     */
    "user"      => "\\app\\admin\\model\\User",

    /**
     * 用户组模型
     */
    "role"      => "\\app\\admin\\model\\Role",

    /**
     * 权限模型
     */
    "rule"      => "\\app\\admin\\model\\Rule",

    /**
     * 用户&权限关联表
     */
    "role_rule" => "role_rule",
];
