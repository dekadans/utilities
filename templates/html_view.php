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

        #color pre {
            margin-bottom: 0;
        }

        #color code {
            background-color: <?= $utilities->getRandomColor() ?>;
        }

        #color p {
            margin-top: 0;
            text-align: center;
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
                <code><?= $request->getUri()->getHost() ?></code>, regardless of method, path, query or request body.
            </p>

            <h3>Status</h3>
            <p>Set the response code using the <code>status</code> query parameter. <a href="?status=418">Like this.</a></p>

            <h3>Media Types</h3>
            <p>
                This document is available in several different representations.
                They can be requested using the <code>Accept</code> header,
                or the <code>_accept</code> query parameter.
            </p>
            <ul>
                <li>
                    <code>text/html</code>
                </li>
                <li>
                    <code><a href="?_accept=application/xml">application/xml</a></code>
                </li>
                <li>
                    <code><a href="?_accept=application/json">application/json</a></code>
                </li>
                <li>
                    <code><a href="?_accept=application/yaml">application/yaml</a></code>
                </li>
                <li>
                    <code><a href="?_accept=application/cbor">application/cbor</a></code>
                </li>
                <li>
                    <code><a href="?_accept=text/plain">text/plain</a></code> <small>(Only HTTP request inspection)</small>
                </li>
            </ul>

            <h4>Additional Resources</h4>
            <ul>
                <li>
                    <a href="/meta/linkset">Linkset (RFC 9264)</a> &dash; Machine-readable index of available resources.
                </li>
                <li>
                    <a href="/meta/schemas/json">JSON Schema</a> &dash; Describing the JSON, YAML and CBOR resources.
                </li>
                <li>
                    <a href="/meta/schemas/xml">XML Schema (XSD)</a> &dash; Describing the XML resource.
                </li>
            </ul>
        </details>

        <section>
            <h2>Date &amp; Time</h2>
            <table>
                <tbody>
                    <tr>
                        <td>UTC - ISO 8601</td>
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
                    <tr>
                        <td>Week Number</td>
                        <td><?= $utilities->getWeek() ?></td>
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
                        <td>Coin flip</td>
                        <td><?= $utilities->getBool() ? 'Heads' : 'Tails' ?></td>
                    </tr>
                    <tr>
                        <td><abbr title="Random UUID - RFC 4122 version 4.">UUID</abbr></td>
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

            <h3>Color</h3>
            <div id="color">
                <pre><code>&nbsp;</code></pre>
                <p><small><?= $utilities->getRandomColor() ?></small></p>
            </div>

            <h3>Lorem Ipsum</h3>
            <?php foreach ($utilities->getSentences() as $item): ?>
                <p><?= $item ?></p>
            <?php endforeach; ?>

            <h3>Bytes</h3>
            <p>32 random and cryptographically secure bytes as a hexadecimal string or a series of decimal integers.</p>
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