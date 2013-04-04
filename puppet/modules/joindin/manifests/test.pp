class joindin::test {

    # only include the test suite if required
    if $params::tests == true {
        include joindin::test::test
    }

}