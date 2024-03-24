<?php
/**
 * @var string $about
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
    <title>utilities.</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        #request-body-container code {
            font-size: 0.9rem;
        }
        #request-body-container pre {
            line-height: 1;
            tab-size: 2;
        }
    </style>
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
        <p><?= $about ?></p>
        <details>
            <summary>Features</summary>
            <ul>
                <li>
                    JSON &amp; XML versions, using the links above or the <code>Accept</code> header.
                    <ul>
                        <li>Schemas are available for both formats.</li>
                    </ul>
                </li>
                <li>
                    Date &amp; time information in various formats and for locations around the world.
                </li>
                <li>
                    Useful randomly generated values.
                </li>
                <li>
                    HTTP request inspection.
                </li>
                <li>
                    Explicitly set the HTTP response code using the <code>status</code> query parameter.
                    For example: <a href="?status=404">?status=404</a>.
                </li>
                <li>
                    Request body hashing and encoding.
                </li>
            </ul>
            <p>
            The source code is available at <a href="https://github.com/dekadans/utilities">Github</a>.
            </p>
        </details>

        <section>
            <h2>Date &amp; Time</h2>
            <p>
                The current UTC date and time in a few common formats.
            </p>
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

            <details>
                <summary>World Time</summary>
                <table>
                    <thead>
                        <tr>
                            <th>Zone</th>
                            <th>Time</th>
                            <th>Offset</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilities->getWorldTime() as $tz => $tzData): ?>
                        <tr>
                            <td><?= $tz ?></td>
                            <td><?= $tzData['time'] ?></td>
                            <td><?= $tzData['offset'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
        </section>
        <section>
            <h2>Random Values</h2>
            <table>
                <tbody>
                    <tr>
                        <td><abbr title="Random version 4 UUID">UUID</abbr></td>
                        <td><?= $utilities->getUuid() ?></td>
                    </tr>
                    <tr>
                        <td>
                            <abbr title="A random string of 20 characters, including upper- and lowercase characters, numbers and symbols.">
                            String
                            </abbr>
                        </td>
                        <td><?= htmlspecialchars($utilities->getPassword()) ?></td>
                    </tr>
                    <tr>
                        <td>
                            <abbr title="A random phrase of six English words. From EFF's diceware list.">
                            Phrase
                            </abbr>
                        </td>
                        <td><?= $utilities->getPhrase() ?></td>
                    </tr>
                </tbody>
            </table>

            <h3>Lorem Ipsum</h3>
            <p class="notice">
                <?= implode('<br><br>', $utilities->getSentences()) ?>
            </p>

            <h3>Bytes</h3>
            <p>32 random and cryptograhpically secure bytes as a hexadecimal string or a series of decimal integers.</p>
            <pre><code><?= $utilities->getBytesHex() ?></code></pre>
            <p class="notice"><?= implode(', ', $utilities->getBytesInt()) ?></>

        </section>

        <section>
            <h2>HTTP Request</h2>

            <p>
                Information about the request that generated this document.
            </p>

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

            <div id="request-body-container">
                <h3 id="request-body">Body</h3>
                <?php if ($body->hasBody()): ?>
                    <pre><code><?= htmlspecialchars($body->getRaw()) ?></code></pre>

                    <details>
                        <summary>Hash</summary>
                        <p>
                            MD5:<br>
                            <code><?= md5($body->getRaw()) ?></code>
                        </p>
                        <p>
                            SHA-1:<br>
                            <code><?= sha1($body->getRaw()) ?></code>
                        </p>
                        <p>
                            SHA-256:<br>
                            <code><?= hash('sha256', $body->getRaw()) ?></code>
                        </p>
                    </details>

                    <details>
                        <summary>Base64</summary>
                        <pre><code><code><?= base64_encode($body->getRaw()) ?></code></code></pre>
                    </details>
                <?php endif; ?>

                <form method="post" action="#request-body">
                    <p>
                        <label for="_body">
                            Send a POST request with a given body.
                            The data will be hashed and encoded using various algorithms.
                        </label>
                        <textarea required id="_body" name="_body" rows="6"></textarea>
                    </p>
                    <p>
                        <button>Send</button>
                    </p>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>utilities. | © 2024 <a href="https://tthe.se">tthe.se</a></p>
    </footer>
</body>
</html>