#Написание основных компонентов (Моделей, Видов, Контроллеров, Виджеты и Форм)

##Особенности написания Контроллеров

- Контроллер выполняющий действия ПУ должен наследоваться от класса AdminController.
- Контроллер выполняющий действия публичной части проекта должен наследоваться от класса BaseController.
- Каждый контроллер должен содержать метод actionsTitles, который возвращает массив с описанием действий.
    Это нужно для:
    1. Логирования действий происходящих на сайте.
        Построения верхнего меню ПУ (из самых используемых Администратором сайта модулей).
    2. Для разграничения прав доступа, на основе построенных заголовков создаются операции и задачи.
    3. Заголовок страницы, берется из них, если если не будет задан иной.

Пример:

~~~
[php]
public static function actionsTitles()
{
    return array(
        "View"   => "Просмотр документа",
        "Create" => "Добавление документа",
        "Update" => "Редактирование документа",
        "Delete" => "Удаление документа",
        "Manage" => "Управление документами",
    );
}
~~~

##Особенности написания View

- Представления для публичной части могут быть различными, но они не должны содержать бизнес-логики. Вся бизнес-логика должна быть определена в моделях и контроллерах.
- View скрипты не должны содержать в себе css и js код. Этот код должен находиться в js и css файлах, а файлы располагаться в директории assets текущего модуля.

##Особенности написания Model

- Модель, которая связана с таблицей в БД, должна наследоваться от класса ActiveRecordModel. Для составления модели необходимо использовать базовые методы и возможности (например такие как: валидатор, scopes, события которые форматируют дату в читабельный вид, и т.д.)
- Модель имеющая поля, которые являются файлами, загружаемыми на сервер,
которые задаются константами, имена констант строятся по принципу
`UPLOAD_DIR_{some} (UPLOAD_DIR_DOCUMENTS = 'upload/news/documents', UPLOAD_DIR_PHOTOS = 'upload/news/photos');`
При этом константа `UPLOAD_DIR = 'upload/news'` должна быть определена обязательно.
- Загрузка файлов на сервер должна быть описана на основе базового класса модели. Для этого нужно описать поля в модели:

~~~
[php]
public function uploadFiles()
{
    return array(
        'photo' => array(
            'dir' => self::FILES_DIR
        )
    );
}
~~~

- Чтобы избежать написания лишних запросов к БД в моделях должны быть описаны связи с таблицами в методе relations,.
- Значения по умолчанию, размеры контейнеров, таймауты и сообщения пользователям должны быть определены константами, например:
`const STATUS_ACTIVE  = 'active';
const STATUS_NEW     = 'new';
const STATUS_BLOCKED = 'blocked';
const GENDER_MAN   = "man";
const GENDER_WOMAN = "woman";
const MAX_AGE = 80;`
- Если предусмотрен адрес просмотра, редактирования объекта модели,
    то во View не дублируется адрес, а модель должна содержать
    метод `getHref, getUpdateHref, getDeleteHref.`



##Особенности написания Форм

- Если на сайте определен единый дизайн для форм ввода, с целью исключения дублирования
разметки формы, должен использоваться встроеннымй механизм Yii -
[построитель форм](http://yiiframework.ru/doc/guide/ru/form.builder).
- Так же можно использовать механизм [вложенных форм](http://yiiframework.ru/doc/guide/ru/form.builder)

**Пример создания документа в ПУ**

В контроллере:
~~~
[php]
public function actionCreate()
{
    $model = new Document;
    $form = new BaseForm('documents.DocumentForm', $model);
    if(isset($_POST['Document']))
    {
        $model->attributes = $_POST['Document'];
        if($model->save())
        {
            $this->redirect(array('view', 'id' => $model->id));
        }
    }
    $this->render('create', array(
        'form' => $form,
    ));
}
//documents.DocumentForm - это форма документа которая находится по адреcу protected/modules/documents/forms/DocumentForm.php
~~~

В `create.php`:

~~~
[php]
echo $form
~~~

В форме следующий код:
~~~
[php]
return array(
    'activeForm' => array(
        'id' => 'document-form'
    ),
    'elements' => array(
        'is_published' => array('type' => 'checkbox')
        'name'         => array('type' => 'text'),
        'desc'         => array('type' => 'editor'),
        'date_publish' => array('type' => 'date'),
    ),
    'buttons' => array(
        'submit' => array('type' => 'submit', 'value' => $this->model->isNewRecord ? 'Добавить' : 'Сохранить')
    )
);
~~~

**Шаблоны вывода форм:**

Есть 2 шаблона вывода форм

- клиентский: `protected/views/layouts/_form`.
- административный: `protected/views/layouts/_adminForm`

Они нужны для того чтобы:

- Унифицировать дизайн форм
- Удобно добавлять новые виджеты
- Удобно подменять один виджет другим


##Написание Виджетов и прочих компонентов

При использовании архитектуры приложения с множеством модулей,
становится сложно найти какой компонент принадлежит какому модулю.
Так же компоненты должны иметь возможность перемещения между модулями, без их переписывания.

Поэтму следует придерживаться следующих правил написания компонентов внутри модуля.

- Обращение к родительскому модулю: `$this->module`
- Обращение к `assets` родительского модуля: `$this->assets`

Этот функционал осуществляется поведением: `ComponentInModuleBehavior`