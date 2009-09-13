<?php menu_pagetitle('Event Managers'); ?>

<div class="box">
    <h1>Managers for <?= $event->getTitle() ?></h1>
    
    <?php $this->load->view('message/area'); ?>
    
    <?php if(count($event->getManagers()) === 0) : ?>
    <p>
        No managers for this event were appointed.
    </p>
    <?php else : ?>
    
    <table class="data-table">
        <thead>
            <tr>
                <td>Full Name</td>
                <td>Username</td>
                <td>Email</td>
                <td style="width: 50px">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($event->getManagers() as $user) : ?>
            <tr>
                <td><?= $user->getDisplayName() ?></td>
                <td><a href="/account/view/<?= $user->getId() ?>"><?= $user->getUsername() ?></a></td>
                <td><?= $user->getEmail() ?></td>
                <td>
                    <?= delete_link("/event/delmanager/{$event->getId()}/{$user->getId()}", 'Are you sure you want to delete this manager?'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php endif; ?>
    
    <div class="right">
        <a href="/event/view/<?= $event->getId(); ?>">back to event</a>
    </div>

    <div>
        <h2>Add Manager</h2 >
        <?= form_open("/event/addmanager/{$event->getId()}"); ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" /><br />
            <input type="submit" id="add" name="add" value="Add user" />
        <?= form_close(); ?>
    </div>
</div>
