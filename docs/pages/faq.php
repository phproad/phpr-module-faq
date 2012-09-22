<h2>Questions</h2>
<? foreach ($categories as $category): ?>
    <h3><?=$category->name?></h3>
    <ol>
        <? foreach ($category->questions as $question): ?>
            <li><a href="#q-<?=$category->id?>-<?=$question->id?>"><?=$question->title?></a></li>
        <? endforeach ?>
    </ol>
<? endforeach ?>

<h2>Answers</h2>
<? foreach ($categories as $category): ?>
    <h3><?=$category->name?></h3>
    <ol>
        <? foreach ($category->questions as $question): ?>

            <li>

                <h5><a name="q-<?=$category->id?>-<?=$question->id?>"></a><?=$question->title?></h5>
                <?=$question->description?>
            </li>
        <? endforeach ?>
    </ol>
<? endforeach ?>
