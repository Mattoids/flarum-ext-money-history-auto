<?php

namespace Mattoid\MoneyHistoryAuto\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Mattoid\MoneyHistory\Event\MoneyAllHistoryEvent;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Mattoid\OperateLog\model\UserOperateLog;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserSaveMiddleware implements MiddlewareInterface
{
    private $events;
    private $source = "USERWILLBESAVED";
    private $sourceDesc = '系统/管理员发放';

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

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes');
        if ($response->getStatusCode() == 200 && strpos($request->getUri(), '/users/') && $request->getMethod() == 'PATCH' && $actor->money != $attributes['money']) {
            $money = (float)$attributes['money'] - $actor->money;
            $actor->init_money = $actor->money;
            $actor->money = $attributes['money'];
            $this->events->dispatch(new MoneyHistoryEvent($actor, $money, $this->source, $this->sourceDesc));
        }

        return $response;
    }
}
