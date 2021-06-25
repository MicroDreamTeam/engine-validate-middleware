## 验证器路由中间件
除了直接实例化验证器并`check`以外，我们还可以使用路由中间件来完成自动验证
## 安装
```shell
composer require itwmw/engine-validate-middleware
```
## 使用
在路由中将对应的验证器中间件引入即可完成自动验证

中间件命名空间：
- Laravel：`Itwmw\Validate\Middleware\Laravel\ValidateMiddleware`
- 软擎：`Itwmw\Validate\Middleware\Rangine\ValidateMiddleware`

其他框架可以根据已提供的中间件逻辑进行编写

为了让中间件能自动完成验证，需要调用`Itwmw\Validate\Middleware\ValidateMiddlewareConfig`的静态方法来设置`自动加载验证器规则`以及设置`自定义规则命名空间前缀`，可设置多个
```php
/**
 * 设置自动加载验证器规则
 * @param string $controllerPath 控制器路径
 * @param string $validatePath   验证器路径
 * @return $this
 */
setAutoValidatePath(string $controllerPath, string $validatePath)

/**
 * 设置自定义规则命名空间前缀,如设置多个则全部生效
 * @param string $rulesPath 自定义规则命名空间前缀
 * @return $this
 */
setRulesPath(string $rulesPath)
```
示例:
```php
ValidateMiddlewareConfig::instance()->setAutoValidatePath('W7\\App\\Controller\\', 'W7\\App\\Model\\Validate\\');
```
如果需要对控制器指定一个验证器，或者控制器下的某个方法指定一个验证器，或指定验证器下的验证场景，可使用`setValidateLink`方法，可设置多个
```php
/**
 * 设置验证器关联
 *
 * @param string|string[] $controller 控制器完整命名空间
 *                                    如需指定方法，请传数组，第二个元素为方法名
 * @param string|string[] $validate   验证器完整命名空间
 *                                    如需指定场景，请传数组，第二个元素为场景名
 * @return $this
 */
setValidateLink($controller, $validate)
```
- 如果控制器没有传方法名，而验证器传了场景名，则该控制器下的全部方法都是用指定验证器下的指定场景
- 如果控制器传了方法名，而验证器没有传场景名，使用指定验证器下，场景为默认规则
- 如果控制器和验证器都没有指定第二个参数，则仅为对应控制器指定验证器，场景还会按照默认规则进行
- 如果控制器和验证都指定了第二个参数，则控制器指定的方法使用验证器指定的场景名进行验证

建议在`Provider`中定义验证相关的设置
## 取值
通过`get_validate_data`方法来获取验证后的值，取回的值为[验证器集合](Collection.md)类型
```php
$data = get_validate_data($request);
```
