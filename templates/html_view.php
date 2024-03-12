<?php
/**
 * @var \tthe\UtilTool\Status $status
 * @var \Psr\Http\Message\ServerRequestInterface $request
 * @var \tthe\UtilTool\RequestBody $body
 * @var \tthe\UtilTool\Utilities $utilities
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTTP Utilities</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <nav>
            <a href="?format=json">JSON</a>
            <a href="?format=xml">XML</a>
        </nav>
        <h1><?= $status->code . ' ' . $status->message ?></h1>
    </header>
    <main>
        <p>This is a small utility website that will generate some useful values and print information about the HTTP request.</p>
        <details>
            <summary>Details</summary>
            <p>
                Information! Status code, content type, link to github
            </p>
        </details>

        <section>
            <h2>Date &amp; Time</h2>
            <table>
                <thead>
                    <tr>
                        <th>Format</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ISO 8601</td>
                        <td><?= $utilities->getTimeIso() ?></td>
                    </tr>
                    <tr>
                        <td>HTTP / RFC 7231</td>
                        <td><?= $utilities->getTimeHttp() ?></td>
                    </tr>
                    <tr>
                        <td>Unix Epoch</td>
                        <td><?= $utilities->getTimeUnix() ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section>
            <h2>Random Values</h2>
            <table>
                <tbody>
                    <tr>
                        <td>UUID</td>
                        <td><?= $utilities->getUuid() ?></td>
                    </tr>
                    <tr>
                        <td>String</td>
                        <td><?= htmlspecialchars($utilities->getPassword()) ?></td>
                    </tr>
                    <tr>
                        <td>Phrase</td>
                        <td><?= $utilities->getPhrase() ?></td>
                    </tr>
                </tbody>
            </table>

            <h3>Lorem Ipsum</h3>
            <p class="notice">
                <?= implode('<br><br>', $utilities->getSentences()) ?>
            </p>

            <h3>Bytes</h3>
            <p>32 random bytes as a hexadecimal string or a series of decimal integers.</p>
            <pre><code><?= $utilities->getBytesHex() ?></code></pre>
            <p class="notice"><?= implode(', ', $utilities->getBytesInt()) ?></>

        </section>

        <section>
            <h2>HTTP Request</h2>

            <p><strong>Method: </strong><?= $request->getMethod() ?></p>

            <h3>Headers</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($request->getHeaders() as $name => $values): ?>
                        <?php foreach ($values as $value): ?>
                        <tr>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!empty($request->getQueryParams())): ?>
                <h3>Query</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($request->getQueryParams() as $name => $value): ?>
                    <tr>
                        <td><?= htmlspecialchars($name) ?></td>
                        <td><?= htmlspecialchars($value) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($body->hasBody()): ?>
                <h3>Body</h3>
                <pre><code><?= htmlspecialchars($body->getRaw()) ?></code></pre>
                <table>
                    <thead>
                        <tr>
                            <th>Algorithm</th>
                            <th>Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MD5</td>
                            <td><?= md5($body->getRaw()) ?></td>
                        </tr>
                        <tr>
                            <td>SHA-1</td>
                            <td><?= sha1($body->getRaw()) ?></td>
                        </tr>
                        <tr>
                            <td>SHA-256</td>
                            <td><?= hash('sha256', $body->getRaw()) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>HTTP Utilities</p>
    </footer>
</body>
</html>