<!-- Файл: src/Template/Articles/index.ctp -->

<h1>Статьи блога</h1>
<?= $this->Html->link('Добавить статью', ['action' => 'add']) ?>
<table>
    <tr>
        <th>Id</th>
        <th>Заголовок</th>
        <th>Дата создания</th>
    </tr>

    <!-- Здесь мы проходимся в цикле по объекту запроса $articles, выводя данные статьи -->

    <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= $article->id ?></td>
        <td>
            <?= $this->Html->link($article->title, ['action' => 'view', $article->id]) ?>
        </td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>