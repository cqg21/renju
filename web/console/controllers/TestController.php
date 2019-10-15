<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/12/2017
 * Time: 23:11
 */

namespace console\controllers;


use common\components\BoardTool;
use common\components\ForbiddenPointFinder;
use common\components\GameStatistics;
use common\components\Gateway;
use common\components\MsgHelper;
use common\components\RenjuBoardTool_bit;
use common\models\BoardWinStatistics;
use common\models\Games;
use common\services\GameService;
use common\services\UserService;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionMsg()
    {
        Gateway::sendToGroup(1,MsgHelper::build('game_info',[
            'game' => GameService::renderGame(1)
        ]));

        $client_list = Gateway::getClientSessionsByGroup(1);
        UserService::render($client_list,'uid');
        Gateway::sendToGroup(1,MsgHelper::build('client_list',[
            'client_list' => $client_list
        ]));
    }

    public function actionGames()
    {
        GameService::sendGamesList();
    }

    public function actionA5()
    {
        $board = new ForbiddenPointFinder('');
        var_dump($board->AddStone(7,7,0));
        //var_dump(BoardTool::a5_symmetry('88798a99','9a7a'));
    }

    public function actionPack()
    {
        GameStatistics::do_record('8889878698789a76979979a696a78aaaa9b89bac8b7c7b6b8d8cabbbba',2);
    }
}