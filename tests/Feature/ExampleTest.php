<?php

// Root route redirects to the public logbook form — assert the redirect.
test('the application root redirects to the logbook form', function () {
    $response = $this->get('/');

    $response->assertRedirect('/logbook');
});
