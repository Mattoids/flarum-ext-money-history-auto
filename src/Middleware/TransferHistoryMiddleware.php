<?php

namespace Mattoid\MoneyHistoryAuto\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
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

class TransferHistoryMiddleware implements MiddlewareInterface
{
    private $events;
    private $source = "TRANSFERMONEY";
    private $sourceKey;
    private $sourceDesc;

    public function __construct(Dispatcher $events, Translator $translator)
    {
        $this->events = $events;
        $this->sourceKey = "mattoid-money-history-auto.forum.searching-recipient";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.searching-recipient");
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($actor, 'id');

        $response = $handler->handle($request);

        if ($response->getStatusCode() === 201 && strpos($request->getUri(), "/transferMoney")) {
            $moneyTransfer = Arr::get($request->getParsedBody(), 'data.attributes.moneyTransfer');
            $selectedUsers = json_decode(Arr::get($request->getParsedBody(), 'data.attributes.selectedUsers'), true);

            $actor->money -= $moneyTransfer * count($selectedUsers);
            $this->events->dispatch(new MoneyHistoryEvent($actor, -$moneyTransfer * count($selectedUsers), $this->source, $this->sourceDesc));

            $userList = User::query()->selectRaw("*, '{$userId}' as create_user_id")->where("id", $selectedUsers)->get();

            $this->events->dispatch(new MoneyAllHistoryEvent($userList, $moneyTransfer, $this->source, $this->sourceDesc));
        }

        return $response;
    }
}
