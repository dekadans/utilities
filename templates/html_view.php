<?php
/**
 * @var string $about
 * @var \tthe\UtilTool\Status $status
 * @var \Psr\Http\Message\ServerRequestInterface $request
 * @var \tthe\UtilTool\RequestBody $body
 * @var \tthe\UtilTool\Utilities $utilities
 * @var \tthe\UtilTool\HttpRepresentation $httpRepr
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>utilities.</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        section {
            margin-top: 5rem;
        }

        table {
            table-layout: auto;
        }

        .http-method {
            color: #0033b3;
        }
        .http-path {
            color: #067d17;
        }
        .http-protocol {
            color: #9e880d;
        }
        .http-header {
            color: #174ad4;
        }
        .http-header-value {
            color: #000000;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $status->code . ' ' . $status->message ?></h1>
        <hr>
    </header>
    <main>
        <p><?= $about ?></p>

        <details>
            <summary>Usage</summary>

            <h3>General</h3>
            <p>
                This application will respond to (almost) any request to the base host
                <code><?= $request->getUri()->getHost() ?></code>, regardless of path, query or request body.
            </p>

            <h3>Status</h3>
            <p>Set the response code using the <code>status</code> query parameter. <a href="?status=418">Like this.</a></p>

            <h3>Media Types</h3>
            <p>
                This document is available in several different representations.
                They can be requested using the <code>Accept</code> header.
                For clients (like browsers) that can't easily modify headers,
                the <code>_accept</code> query parameter can be used as an alternative.
            </p>
            <ul>
                <li>
                    <code><a href="?_accept=application/json">application/json</a></code> <small>(<a href="/meta/schemas/json">Schema</a>)</small>
                </li>
                <li>
                    <code>text/html</code>
                </li>
                <li>
                    <code><a href="?_accept=text/plain">text/plain</a></code> <small>(Only HTTP request inspection)</small>
                </li>
                <li>
                    <code><a href="?_accept=application/xml">application/xml</a></code> <small>(<a href="/meta/schemas/xml">Schema</a>)</small>
                </li>
                <li>
                    <code><a href="?_accept=application/yaml">application/yaml</a></code>
                </li>
            </ul>
            <p>
                A <abbr title="See RFC 9264 (Linkset: Media Types and a Link Relation Type for Link Sets)">linkset</abbr>
                with a machine-readable index of available resources is <a href="/meta/linkset">available here</a>.
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
            <p>
                <?= implode('<br><br>', $utilities->getSentences()) ?>
            </p>

            <h3>Bytes</h3>
            <p>32 random and cryptograhpically secure bytes as a hexadecimal string or a series of decimal integers.</p>
            <pre><code><?= $utilities->getBytesHex() ?></code></pre>
            <pre><code><?php
            foreach (array_chunk($utilities->getBytesInt(), 8) as $chunk) {
                echo implode("\t", $chunk) . "\n";
            }
            ?></code></pre>

        </section>

        <section>
            <h2 id="request">HTTP Request</h2>

            <p>
                This is an approximate representation of the HTTP request that generated this document.
            </p>

            <pre><code><?= $httpRepr->generateForHtml() ?></code></pre>

            <?php if ($body->hasBody()): ?>
                <h3>Body</h3>
                <h4>Hashes</h4>
                <table>
                    <tbody>
                        <tr>
                            <td>MD5</td>
                            <td><code><?= md5($body->getRaw()) ?></code></td>
                        </tr>
                        <tr>
                            <td>SHA-1</td>
                            <td><code><?= sha1($body->getRaw()) ?></code></td>
                        </tr>
                        <tr>
                            <td>SHA-256</td>
                            <td><code><?= hash('sha256', $body->getRaw()) ?></code></td>
                        </tr>
                    </tbody>
                </table>
    
                <h4>Base64</h4>
                <pre><code><code><?= chunk_split(base64_encode($body->getRaw())) ?></code></code></pre>
            <?php endif; ?>
        </section>
        <section>
            <hr>
            <form method="post" action="<?= $request->getUri() ?>#request">
            <p>
                <label for="_body">
                    Request body:
                </label>
                <textarea required id="_body" name="_body" rows="6"><?= $body->getRaw() ?? '' ?></textarea>
                <small>Requests sent with a message body will have its contents hashed and base64 encoded.</small>
            </p>
            <p>
                <button>Send POST</button>
            </p>
        </form>
        </section>
    </main>

    <footer>
        <p>utilities. | <a href="https://github.com/dekadans/utilities">Github</a> | © 2024 <a href="https://tthe.se">tthe.se</a></p>
    </footer>
</body>
</html>