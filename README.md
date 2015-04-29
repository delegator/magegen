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

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## License

All files released in this repository are released under the Apache License,
Version 2.0 and copyright Delegator LLC -- UNLESS OTHERWISE NOTED IN THE FILE.
