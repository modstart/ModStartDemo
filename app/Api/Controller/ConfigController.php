<?php

namespace App\Api\Controller;

use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Auth\MemberVip;
use Module\Vendor\Cache\LazyValueUtil;

class ConfigController extends BaseController
{
    public function app()
    {
        $data = [];
        list($view, $_) = $this->viewPaths('index');
        $hash = date('Ymd_His', filemtime($this->viewRealpath($view)));
        $data['hashPC'] = 'v' . $hash;
        $data['hashLazyValue'] = [];

        $data['user'] = [
            'id' => 0,
            'avatar' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'avatarMedium' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'avatarBig' => AssetsUtil::fixFull('asset/image/avatar.png'),
            'nickname' => '',
            'username' => '',
            'phone' => '',
            'phoneVerified' => false,
            'email' => '',
            'emailVerified' => false,
            'vip' => null,
            'vipExpire' => null,
        ];
        if (MemberUser::id()) {
            $memberUser = MemberUser::user();
            $data['user']['id'] = $memberUser['id'];
            $data['user']['avatar'] = AssetsUtil::fixFull($memberUser['avatar'] ? $memberUser['avatar'] : $data['user']['avatar']);
            $data['user']['avatarMedium'] = AssetsUtil::fixFull($memberUser['avatarMedium'] ? $memberUser['avatarMedium'] : $data['user']['avatar']);
            $data['user']['avatarBig'] = AssetsUtil::fixFull($memberUser['avatarBig'] ? $memberUser['avatarBig'] : $data['user']['avatar']);
            $data['user']['username'] = $memberUser['username'];
            $data['user']['nickname'] = empty($memberUser['nickname']) ? null : $memberUser['nickname'];
            if (empty($data['user']['nickname'])) {
                $data['user']['nickname'] = $data['user']['username'];
            }
            $data['user']['phone'] = $memberUser['phone'];
            $data['user']['phoneVerified'] = !!$memberUser['phoneVerified'];
            $data['user']['email'] = $memberUser['email'];
            $data['user']['emailVerified'] = !!$memberUser['emailVerified'];
            $data['user']['vip'] = MemberVip::get();
            $data['user']['vipExpire'] = $memberUser['vipExpire'];
        }

        return Response::jsonSuccessData($data);
    }


}
