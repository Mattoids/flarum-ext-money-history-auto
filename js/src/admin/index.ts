import app from 'flarum/admin/app';

app.initializers.add('mattoid/flarum-ext-money-history-auto', () => {
  app.extensionData.for("mattoid-money-history-auto")
    .registerSetting({
      setting: 'mattoid-money-history-auto.privateChatsAreNotRewarded',
      help: app.translator.trans('mattoid-money-history-auto.admin.private-chats-are-not-rewarded-requirement'),
      label: app.translator.trans('mattoid-money-history-auto.admin.private-chats-are-not-rewarded'),
      type: 'switch',
    })
});

