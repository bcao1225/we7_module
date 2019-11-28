<?php
/**
 * gather_feedback模块微站定义
 *
 * @author QQRPazWaNPgW
 * @url
 */

defined('IN_IA') or exit('Access Denied');

class Gather_feedbackModuleSite extends WeModuleSite
{
    /**
     * 给后添加后缀
     * @param $child_list '选项集合，arr类型'
     * @param $type '1、letter字母类型，2、num数字类型 3、void没有后缀'
     * @return mixed
     */
    public function addPrefix($child_list, $type)
    {
        if ($type === 'void') return;

        $str = $type === 'letter' ? 'A' : 1;
        foreach ($child_list as $key => $child) {
            $child_list[$key]['title'] = $str . '、' . $child['title'];
            $str++;
        }

        return $child_list;
    }

    public function get_question(){
        return 'hello';
    }
}