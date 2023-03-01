<form name='sms'>
    <label>
        <?= __('From') ?>
        <input class='full-width' maxlength='16' name='from'/>
    </label>

    <label>
        <?= __('To') ?>
        <input class='full-width' name='to' required value='<?= $recipient ?>'/>
    </label>

    <div>
        <label>
            <?= __('Flash?') ?>

            <input type='checkbox' name='flash' value='1'/>
        </label>

        <label>
            <?= __('Performance Tracking?') ?>

            <input type='checkbox' name='performance_tracking' value='1'/>
        </label>
    </div>

    <label>
        <?= __('Text') ?>

        <textarea class='full-width' maxlength='1520' name='text' required rows='8'></textarea>
    </label>

    <button class='btn btn-primary' type='button'><?= __('Submit') ?></button>
</form>

<script>
    const form = document.forms.namedItem('sms')

    form.querySelector('button').addEventListener('click', async e => {
        e.preventDefault()

        const valid = form.reportValidity()

        if (!valid) return

        const body = new FormData(form)
        body.set('json', '1')

        const headers = {
            SentWith: 'osTicket',
            'X-Api-Key': '<?= $apiKey ?>',
        }

        const res = await fetch('https://gateway.seven.io/api/sms', {
            body,
            headers,
            method: 'POST',
        })
        const json = await res.json()

        form.insertAdjacentText('afterend', JSON.stringify(json, null, 4))
    })
</script>
