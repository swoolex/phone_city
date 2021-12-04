<?php
/**
 * +----------------------------------------------------------------------
 * 国内手机号归属地解析
 * +----------------------------------------------------------------------
 * 官网：https://www.sw-x.cn
 * +----------------------------------------------------------------------
 * 作者：小黄牛 <1731223728@qq.com>
 * +----------------------------------------------------------------------
 * 开源协议：http://www.apache.org/licenses/LICENSE-2.0
 * +----------------------------------------------------------------------
*/

namespace Swx\PhoneCity;


class PhoneCity {
    /**
     * 当前版本号
    */
    private $version = '1.0.1';
    /**
     * 失败原因
    */
    private $error = '';
    /**
     * 结果集
    */
    private $data = [];

    /**
     * 调用入口
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2021-12-03
     * @deprecated 暂不启用
     * @global 无
     * @param int $phone 手机号码
     * @return false.array
    */
    public function handle($phone) {
        if (empty($phone)) {
            $this->error = '手机号码为空';
            return false;
        }
        if (!preg_match("/^1[23456789]\d{9}$/", $phone)) {
            $this->error = '手机号格式错误';
            return false;
        }
        $prefix = substr($phone, 0, 3);
        $area_code = substr($phone, 3, 4);
        $path = __DIR__ . DIRECTORY_SEPARATOR .'data'. DIRECTORY_SEPARATOR .$prefix. '.php';
        if (!file_exists($path)) {
            $this->error = '手机号识别失败，建议通知SW-X开发组成员，更新归属地址库';
            return false;
        }
        $list = require $path;
        $region = require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'region_map.php';
        if (!isset($list[$area_code])) {
            $this->error = '手机号识别失败，建议通知SW-X开发组成员，更新归属地址库';
            return false;
        }
        
        $data = $list[$area_code];
        switch ($data[0]) {
            case 1: $isp = '移动';break;
            case 2: $isp = '联通';break;
            case 3: $isp = '电信';break;
        }
        $region_data = $region[$data[1]];
        
        $this->data = [
            'segment_no' => $prefix,
            'area_code' => $area_code,
            'isp' => $isp,
            'province' => $region_data['a'],
            'city' => $region_data['b']
        ];

        return $this->data;
    }

    /**
     * 获取失败原因描述
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2021-12-03
     * @deprecated 暂不启用
     * @global 无
     * @return string
    */
    public function error() {
        return $this->error;
    }

    /**
     * 成员属性的方式读取结果集
     * @todo 无
     * @author 小黄牛
     * @version v1.0.1 + 2021-12-03
     * @deprecated 暂不启用
     * @global 无
     * @param string $name
     * @return mixed
    */
    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

}
