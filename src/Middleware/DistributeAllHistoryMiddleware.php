<?php

namespace Mattoid\MoneyHistoryAuto\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Mattoid\MoneyHistory\Event\MoneyAllHistoryEvent;
use Mattoid\OperateLog\model\UserOperateLog;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DistributeAllHistoryMiddleware implements MiddlewareInterface
{
    private $events;
    private $source = "BABCHDISTRIBUTION";
    private $sourceDesc = "系统奖励";

    public function __construct(Dispatcher $events, Translator $translator)
    {
        $this->events = $events;
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.system-rewards");
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($actor, 'id');

        $response = $handler->handle($request);

        $dryRun = Arr::get($request->getParsedBody(), 'dryRun');
        if ($response->getStatusCode() === 200 && !$dryRun && strpos($request->getUri(), "/money-to-all")) {

            $amount = Arr::get($request->getParsedBody(), 'amount');

            $userList = User::query()->selectRaw("*, '{$userId}' as create_user_id")->get();

            $this->events->dispatch(new MoneyAllHistoryEvent($userList, $amount, $this->source, $this->sourceDesc));
        }



        return $response;
    }
}
