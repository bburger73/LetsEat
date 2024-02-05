import React from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function Settings(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );


    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );



    const [email, updateEmail] = React.useState(props.user.email);
    const [pass, updatePass] = React.useState();
    const [passver, updatePassVer] = React.useState();
    const [name, setName] = React.useState(props.user.name);
    const [selected, updateSelected] = React.useState(props.user.notificationSelect);
    const toast = useToast();
    const [isLoadingEmail, setLoadingEmail] = React.useState(false);
    const [isLoadingPassword, setLoadingPassword] = React.useState(false);
    const [isLoadingName, setLoadingName] = React.useState(false);
    const [isLoadingNotification, setLoadingNotification] = React.useState(false);

    const emailRef = React.useRef(null);
    const passwordRef = React.useRef(null);
    const passwordVerRef = React.useRef(null);
    const nameRef = React.useRef(null);
    // const Ref = React.useRef(null);

    const updateSelect = (val) => {
        setLoadingNotification(true);
        updateSelected(val);
        let hold = "0";
        if (val) {
            hold = "1";
        }
        const requestOptions = {
            method: "PUT",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                notification: hold
            }),
        };

        fetch(global.server + "user_account/update.php", requestOptions).then(res => res.json()).then(result => {
            if (result.results.notification) {
                alert("Notifications Updated Successfully");
            } else {
                alert("Notifications Not Updated");
            }
        }).catch(() => {
            setTimeout(() => {
                setLoadingNotification(false);
                alert("Failed To Connect To Server");
            }, 2000);
        }).finally(() => {
            setTimeout(() => setLoadingNotification(false), 2000);
        });
    }

    const confirmUpdateEmail = () => {
        setLoadingEmail(true);
        if (email !== null && email !== undefined && email !== "") {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    userId: props.user.id,
                    email: email
                }),
            };

            fetch(global.server + "user_account/update.php", requestOptions).then(res => res.json()).then(result => {
                if (result.results.email) {
                    alert("Email Updated Successfully!");
                } else {
                    alert("Email Not Updated");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingEmail(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingEmail(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingEmail(false);
        }
    }

    const confirmUpdateName = () => {
        setLoadingName(true);
        if (name !== null && name !== undefined && name !== "") {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    userId: props.user.id,
                    name: name
                }),
            };

            fetch(global.server + "user_account/update.php", requestOptions).then(res => res.json()).then(result => {
                if (result.results.name) {
                    alert("Name Updated Successfully!");
                } else {
                    alert("Name Not Updated");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingName(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingName(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingName(false);
        }
    }

    const confirmUpdatePassword = () => {
        setLoadingPassword(true);
        if (pass === passver && pass !== null && pass !== undefined) {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    userId: props.user.id,
                    pass: pass
                }),
            };

            fetch(global.server + "user_pass/update.php", requestOptions).then(res => res.json()).then(result => {
                // console.log(result);
                if (result.password) {
                    alert("Password Updated Successfully!");
                } else {
                    alert("Password Not Updated");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingPassword(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingPassword(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingPassword(false);
        }
    }

    const goback = () => {
        props.pagging(1)
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text>Notifications</Text>

                                {(isLoadingNotification) ?
                                    <ActivityIndicator color={"black"} size="large" /> : <>
                                        <TouchableOpacity
                                            id={true}
                                            name="notification"
                                            onPress={() => updateSelect(true)}
                                        >
                                            <View style={{
                                                flexDirection: 'row'
                                            }}>
                                                <View style={{
                                                    margin: 5,
                                                    height: 24,
                                                    width: 24,
                                                    borderRadius: 12,
                                                    borderWidth: 2,
                                                    borderColor: '#000',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                }}>
                                                    {
                                                        selected ?
                                                            <View style={{
                                                                height: 12,
                                                                width: 12,
                                                                borderRadius: 6,
                                                                backgroundColor: '#000',
                                                            }} />
                                                            : null
                                                    }
                                                </View>
                                                <Text style={{
                                                    color: "black",
                                                    fontSize: 20,
                                                    margin: 5
                                                }}
                                                >On</Text>
                                            </View>
                                        </TouchableOpacity>
                                        <TouchableOpacity
                                            id={false}
                                            name="notification"
                                            onPress={() => updateSelect(false)}
                                        >
                                            <View style={{
                                                flexDirection: 'row'
                                            }}>
                                                <View style={{
                                                    margin: 5,
                                                    height: 24,
                                                    width: 24,
                                                    borderRadius: 12,
                                                    borderWidth: 2,
                                                    borderColor: '#000',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                }}>
                                                    {
                                                        !selected ?
                                                            <View style={{
                                                                height: 12,
                                                                width: 12,
                                                                borderRadius: 6,
                                                                backgroundColor: '#000',
                                                            }} />
                                                            : null
                                                    }
                                                </View>
                                                <Text style={{
                                                    color: "black",
                                                    fontSize: 20,
                                                    margin: 5
                                                }}
                                                >Off</Text>
                                            </View>
                                        </TouchableOpacity>
                                    </>}
                                <Text>Email</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={updateEmail}
                                    value={email}
                                    placeholder="John@Doe.com"
                                    onSubmitEditing={() => {
                                        confirmUpdateEmail();
                                    }}
                                />
                                <Button
                                    onPress={() => {
                                        confirmUpdateEmail();
                                    }}
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    title="Update Email"
                                    accessibilityLabel="Update Email"
                                    isLoading={isLoadingEmail}
                                />
                                <Text>Password</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={updatePass}
                                    value={pass}
                                    placeholder="*********"
                                    secureTextEntry={true}
                                    onSubmitEditing={() => {
                                        passwordVerRef.current.focus();
                                    }}
                                />
                                <Text style={((pass === passver) ? (styles.good) : (styles.bad))}
                                >Password Verification</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={updatePassVer}
                                    value={passver}
                                    placeholder="*********"
                                    secureTextEntry={true}
                                    onSubmitEditing={() => {
                                        confirmUpdatePassword();
                                    }}
                                    ref={passwordVerRef}
                                />
                                <Button
                                    onPress={() => {
                                        confirmUpdatePassword();
                                    }}
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    title="Update Password"
                                    accessibilityLabel="Update Password"
                                    isLoading={isLoadingPassword}
                                />
                                <Text>Your Name</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setName}
                                    value={name}
                                    placeholder="John Doe"
                                    onSubmitEditing={() => {
                                        confirmUpdateName();
                                    }}
                                />
                                <Button
                                    onPress={() => {
                                        confirmUpdateName();
                                    }}
                                    title="Update Name"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Update Name"
                                    isLoading={isLoadingName}
                                />
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}