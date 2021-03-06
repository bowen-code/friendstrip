<?php


/*

var data = {
    "spotId": 1,
    "type": 1 // 1 景区，2景点
};

$.post('action/comment_list.php', data, function (res) {
console.log(res);
});



 */

include 'common.class.php';


class Comment extends Common {

    public $data;


    function __construct() {

        parent::__construct();
    }


    public function index() {

        //$get_data = $_POST;
        $post_data = file_get_contents("php://input");
        $get_data = json_decode($post_data, true);

        $sql = "SELECT * FROM ft_comment WHERE spotview_id = :spotview_id AND type = :type ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':spotview_id' => $get_data['spotId'], ':type' => $get_data['type']));
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $res = $stmt->fetchAll();

        if (count($res) > 0) {
            $list['spot_id'] = $get_data['spotId'];
            foreach($res as $k => $v) {
                $sql_user = "SELECT * FROM ft_user WHERE user_id=:user_id";
                $stmt_user = $this->db->prepare($sql_user);
                $stmt_user->execute(array(':user_id' => $v['user_id']));
                $stmt_user->setFetchMode(PDO::FETCH_ASSOC);
                $res_user = $stmt_user->fetch();
                $list['comment'][$k]['uid'] = $res_user['user_id'];
                $list['comment'][$k]['name'] = $res_user['name'];
                $list['comment'][$k]['imageUrl'] = $res_user['avatar'];
                $list['comment'][$k]['content'] = $v['content'];
                $list['comment'][$k]['date'] = date('Y-m-d H:i:s', $v['date']);
            }

            if ($res) {
                $this->res['code'] = 0;
                $this->res['message'] = 'success';
                $this->res['comment'] = $list['comment'];
                $this->res['spot_id'] = $list['spot_id'];
            } else {
                $this->res['code'] = 1035;
                $this->res['message'] = '查询数据库失败';
            }
        } else {
            $this->res['code'] = 1036;
            $this->res['message'] = '结果为空';
        }

    }


    function __destruct() {
        $this->die_json($this->res);
    }
}


$comment = new Comment();
$comment->index();












