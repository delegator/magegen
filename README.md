# Magegen

Magegen is a build system for Magento extensions.

We couldn't found a simple way to build Magento extensions, so we made our own.
This tool works well with (and requires) `modman`. Learn more about `modman`
[here](https://github.com/colinmollenhour/modman). Your extensions modman file
is parsed to create the directory and file listing for your package.xml.

## Installation

We install this tool as a composer dependency in our extensions.

```json
{
    "require-dev": {
        "delegator/magegen": "*"
    }
}
```

## Usage

Before you can use `magegen`, you should have a working `.modman` directory with
a project deployed. In your project's repo directory, add the `magegen`
dependency and update composer with `composer update`.

`magegen` requires that you have a correct `modman` file AND a
package.template.xml file.

To generate a package.template.xml file, run `vendor/bin/magegen init`.

Make sure you edit the package.template.xml file to match your extension's
information. Leave the following nodes empty, `magegen` will fill them in when
building your extension:

* date
* time
* contents

When you are ready to build your extension (generate a .tgz file), navigate
to the root of your modman repo directory and run `vendor/bin/magegen build`

## Hooks

For some projects, you may need to compile assets or move files before building.
To accommodate this, a simple hook system executes your code for the following
events.

|  Hook Name   |       When the hook is executed     |
|--------------|-------------------------------------|
| `pre_build`  | Before the 'build' command executes |
| `post_build` | After the 'build' command executes  |
| `pre_check`  | Before the 'check' command executes |
| `post_check` | After the 'check' command executes  |
| `pre_clean`  | Before the 'clean' command executes |
| `post_clean` | After the 'clean' command executes  |
| `pre_init`   | Before the 'init' command executes  |
| `post_init`  | After the 'init' command executes   |

To execute your code for each of these hooks, create a directory at
`magegen/[hook_name]/`. In that directory, add PHP files that will be executed
as the hook fires.

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## License

Refer to [LICENSE.md](LICENSE.md) for license information.
