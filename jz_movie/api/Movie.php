<?php
require_once __DIR__ . '/Api.php';

defined('IN_IA') or exit('Access Denied');

class Movie extends Api
{
    /**
     * 通过电影code获取电影
     */
    public function code_by_movie()
    {
        global $_GPC, $_W;

        $movie = $this->db->get("movie", ['movie_code' => $_GPC['code']]);

        /*获取导演*/
        $movie['director_code'] =
            $this->db->get("showman", ['showman_code' => $movie['director_code']]);

        /*获得监制*/
        $movie['film_producer_code'] =
            $this->db->get("showman", ['showman_code' => $movie['film_producer_code']]);

        /*制片人*/
        $movie['producer_code'] =
            $this->db->get("showman", ['showman_code' => $movie['producer_code']]);

        /*出品公司*/
        $production_company_code_list = explode(",", $movie['production_company_code']);
        $sql = "SELECT * FROM showman WHERE showman_code IN (";
        foreach ($production_company_code_list as $key => $item) {
            if (end($production_company_code_list) == $item) {
                $sql = $sql . "'$item'";
            } else {
                $sql = $sql . "'$item'" . ',';
            }
        }
        $sql = $sql . ')';
        $movie['production_company_code'] = $this->db->fetchall($sql);

        /*演员*/
        $actor_list = explode(",", $movie['actor_code']);
        $sql = "SELECT * FROM showman WHERE showman_code IN (";
        foreach ($actor_list as $key => $item) {
            if (end($actor_list) == $item) {
                $sql = $sql . "'$item'";
            } else {
                $sql = $sql . "'$item'" . ',';
            }
        }
        $sql = $sql . ')';
        $movie['actor_code'] = $this->db->fetchall($sql);

        /*电影标签*/
        $tag_list = explode(",", $movie['movie_tag_code']);
        $sql = "SELECT * FROM movie_tag WHERE movie_tag_code IN (";
        foreach ($tag_list as $key => $item) {
            if (end($tag_list) == $item) {
                $sql = $sql . "'$item'";
            } else {
                $sql = $sql . "'$item'" . ',';
            }
        }
        $sql = $sql . ')';
        $movie['movie_tag_code'] = $this->db->fetchall($sql);

        /*截取预计上映时间*/
        $movie['estimated_release_time'] = date('Y-m-d', strtotime($movie['estimated_release_time']));

        $this->result(0, "获取成功", $movie);
    }

    /**
     *  获得所有电影
     */
    public function movie_all()
    {
        global $_W, $_GPC;

        if ($_GPC['movie_schedule']) {
            //近七天
            if ($_GPC['movie_schedule'] == 8) {
                $seven = 7 * 24 * 60 * 60;
                $list = $this->db->getall('movie');

                $rest_list = [];

                $current_time = time();

                foreach ($list as $item) {
                    //预计上映时间
                    $estimated = strtotime($item['estimated_release_time']);

                    $arr = [
                        '电影名称' => $item['movie_name'],
                        '预计上映时间' => date('Y-m-d', $estimated),
                        '7天后上映时间' => date('Y-m-d', $estimated + $seven)
                    ];

                    FileLog::println($arr);

                    if ($estimated < $current_time + $seven && $estimated > $current_time) {
                        array_push($rest_list,$item);
                    }
                }

                $this->result(0, '获取成功', $rest_list);
            } else {
                $list = $this->db->getall('movie', ['movie_schedule' => $_GPC['movie_schedule']]);
            }
        } else {
            $list = $this->db->getall('movie');
        }

        $this->result(0, "获取成功", $list);
    }

    /**
     * 搜索具体电影
     */
    public function search_movie()
    {
        global $_GPC, $_W;
        $sql = "SELECT * FROM movie WHERE movie_name LIKE '%" . $_GPC['search'] . "%'";
        $this->result(0, "搜索成功", $this->db->fetchall($sql));
    }
}
